<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Models\AnnualReport;
use App\Models\DailyStat;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\Sponsorship;
use App\Models\TimelineEvent;
use App\Models\User;
use App\Notifications\AnnualImpactBookReadyNotification;
use App\Support\Services\ImpactBookGenerator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportService
{
    protected ImpactBookGenerator $impactBookGenerator;

    public function __construct(ImpactBookGenerator $impactBookGenerator)
    {
        $this->impactBookGenerator = $impactBookGenerator;
    }
    /**
     * Get data for the admin dashboard.
     *
     * @param int|null $branchId
     * @return array
     */
    public function getAdminDashboardData($branchId = null): array
    {
        $latestStat = DailyStat::where('branch_id', $branchId)->latest('date')->first();

        if (!$latestStat) {
            return [
                'summary' => [
                    'total_pledged' => 0,
                    'total_collected' => 0,
                    'gap' => 0,
                    'active_elders' => 0,
                    'matched_elders' => 0,
                    'active_donors' => 0,
                ],
                'chart_data' => [],
            ];
        }

        return [
            'summary' => $latestStat->toArray(),
            'chart_data' => [], // Placeholder for now
        ];
    }

    /**
     * Get impact data for a specific donor.
     *
     * @param User $user
     * @param string $range
     * @return array
     */
    public function getDonorImpactData(User $user, string $range): array
    {
        $data = $this->getCommonDonorData($user, 5, $range);

        $supportedEldersCount = Sponsorship::where('user_id', $user->id)
            ->where('status', 'active')
            ->distinct('elder_id')
            ->count();

        $data['timeline_events'] = $data['keyTimelineEvents'];
        unset($data['keyTimelineEvents']);
        $data['donation_trend'] = $this->donorMonthlyTrend($user, 6);
        $data['ethiopian_date'] = ethiopian_date(now());

        return array_merge($data, ['supported_elders_count' => $supportedEldersCount]);
    }

    /**
     * Get data for the donor's impact book.
     *
     * @param User $user
     * @return array
     */
    public function getImpactBookData(User $user): array
    {
        $commonData = $this->getCommonDonorData($user, 10, 'all');

        $supportedElders = Elder::whereHas('sponsorships', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id)
                ->where('status', 'active');
        })->get();

        return array_merge($commonData, [
            'supportedElders' => $supportedElders,
            'donation_trend' => $this->donorMonthlyTrend($user, 12),
        ]);
    }

    public function generateImpactBookForUser(User $user, ?int $year = null): AnnualReport
    {
        $year ??= now()->year;
        $impactData = $this->getImpactBookData($user);
        $impactData['donation_trend'] = $this->donorMonthlyTrend($user, 12);
        $impactData['ethiopian_date'] = ethiopian_date(now());

        $pdfPath = $this->impactBookGenerator->generate($user, $impactData, $year);

        return AnnualReport::updateOrCreate(
            ['user_id' => $user->id, 'report_year' => $year],
            ['impact_data' => $impactData, 'pdf_path' => $pdfPath]
        );
    }

    /**
     * Get common data for a donor's reports.
     *
     * @param User $user
     * @param int $timelineEventLimit
     * @param string $range
     * @return array
     */
    private function getCommonDonorData(User $user, int $timelineEventLimit, string $range): array
    {
        $donationsQuery = Donation::where('user_id', $user->id)
            ->where('status', 'approved');

        $timelineEventsQuery = TimelineEvent::where('user_id', $user->id);

        if ($range !== 'all') {
            $days = (int)$range;
            $donationsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
            $timelineEventsQuery->where('occurred_at', '>=', Carbon::now()->subDays($days));
        }

        $totalDonations = $donationsQuery->sum('amount');

        $keyTimelineEvents = $timelineEventsQuery
            ->with('elder')
            ->latest('occurred_at')
            ->take($timelineEventLimit)
            ->get();

        return [
            'total_donations' => $totalDonations,
            'keyTimelineEvents' => $keyTimelineEvents,
        ];
    }

    /**
     * Get relationship-based elder recommendations
     */
    public function getEldersByRelationshipPriority(int $branchId = null): array
    {
        return Elder::where('sponsorship_status', 'not_sponsored')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('relationship_priority')
            ->orderBy('priority_level', 'desc')
            ->get()
            ->groupBy('relationship_type')
            ->toArray();
    }

    /**
     * Track promise fulfillment and send notifications
     */
    public function checkMonthlyPromises(): array
    {
        $missedPayments = [];
        $activeSponsorships = Sponsorship::where('status', 'active')
            ->where('start_date', '<=', now()->subMonth())
            ->get();

        foreach ($activeSponsorships as $sponsorship) {
            $lastMonthDonations = Donation::where('user_id', $sponsorship->user_id)
                ->where('elder_id', $sponsorship->elder_id)
                ->where('created_at', '>=', now()->subMonth())
                ->sum('amount');

            $promiseKept = $lastMonthDonations >= $sponsorship->amount;

            $sponsorship->update([
                'promise_kept_last_month' => $promiseKept,
                'consecutive_months_kept' => $promiseKept ?
                    $sponsorship->consecutive_months_kept + 1 : 0,
                'missed_payment_count' => $promiseKept ?
                    $sponsorship->missed_payment_count : $sponsorship->missed_payment_count + 1
            ]);

            if (!$promiseKept) {
                $missedPayments[] = $sponsorship;
                // TODO: Send notification (SMS/Email)
                // $this->sendPaymentReminder($sponsorship);
            }
        }

        return $missedPayments;
    }

    /**
     * Generate annual thank you reports
     */
    public function generateAnnualReports(int $year): int
    {
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        $consistentDonors = User::whereHas('sponsorships', function ($q) {
                $q->where('status', 'active');
            })
            ->whereHas('donations', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'approved');
            })
            ->get();

        $generated = 0;
        foreach ($consistentDonors as $donor) {
            $annualReport = $this->generateImpactBookForUser($donor, $year);
            if ($annualReport->wasRecentlyCreated || $annualReport->wasChanged('pdf_path')) {
                $donor->notify(new AnnualImpactBookReadyNotification($annualReport));
            }

            $generated++;
        }

        return $generated;
    }

    /**
     * Calculate annual impact for a donor
     */
    private function calculateAnnualImpact(User $user, int $year): array
    {
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        $totalDonations = Donation::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $supportedElders = Sponsorship::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('elder')
            ->get();

        $timelineEvents = TimelineEvent::where('user_id', $user->id)
            ->with('elder')
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->latest()
            ->take(10)
            ->get()
            ->map(function (TimelineEvent $event) {
                return [
                    'id' => $event->id,
                    'description' => $event->description,
                    'occurred_at' => $event->occurred_at?->toDateTimeString(),
                    'elder' => $event->elder ? [
                        'id' => $event->elder->id,
                        'name' => $event->elder->name,
                    ] : null,
                ];
            });

        return [
            'total_donations' => $totalDonations,
            'supportedElders' => $supportedElders,
            'supported_elders' => $supportedElders->map(function ($sponsorship) {
                return [
                    'name' => $sponsorship->elder->first_name . ' ' . $sponsorship->elder->last_name,
                    'relationship' => $sponsorship->relationship_type,
                    'months_supported' => $sponsorship->consecutive_months_kept,
                ];
            }),
            'supported_elders_count' => $supportedElders->count(),
            'timeline_events' => $timelineEvents,
            'timeline_events_count' => $timelineEvents->count(),
        ];
    }

    /**
     * Get featured matches for slideshow
     */
    public function getFeaturedMatches(): array
    {
        return Sponsorship::where('status', 'active')
            ->whereHas('elder', fn($q) => $q->where('is_featured', true))
            ->with(['user', 'elder'])
            ->inRandomOrder()
            ->take(10)
            ->get()
            ->map(function($sponsorship) {
                return [
                    'donor_name' => $sponsorship->user->name,
                    'elder_name' => $sponsorship->elder->first_name . ' ' . $sponsorship->elder->last_name,
                    'relationship' => $sponsorship->relationship_type,
                    'months_supported' => $sponsorship->consecutive_months_kept,
                    'story' => "Supporting {$sponsorship->elder->first_name} as their {$sponsorship->relationship_type} for {$sponsorship->consecutive_months_kept} months"
                ];
            })
            ->toArray();
    }

    /**
     * Get comprehensive admin dashboard data
     */
    public function getEnhancedAdminDashboard($branchId = null, int $dateRange = 30): array
    {
        $dateRange = max(7, min($dateRange, 365));
        $cacheKey = sprintf(
            'report:enhanced_admin_dashboard:branch:%s:range:%d',
            $branchId ? $branchId : 'all',
            $dateRange,
        );

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($branchId, $dateRange) {
            return $this->buildAdminDashboardPayload($branchId, $dateRange);
        });
    }

    /**
     * Construct dashboard payload so it can be cached.
     */
    protected function buildAdminDashboardPayload($branchId = null, int $dateRange = 30): array
    {
        // Base query for sponsorships
        $sponsorshipQuery = Sponsorship::query()->whereIn('status', ['active', 'pending', 'fulfilled']);
        if ($branchId) {
            $sponsorshipQuery->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        // 1. Total Sponsorships
        $totalSponsorships = (clone $sponsorshipQuery)->count();

        // 2. Relationship Distribution
        $relationshipDistribution = (clone $sponsorshipQuery)
            ->select('relationship_type', \DB::raw('count(*) as count'))
            ->groupBy('relationship_type')
            ->pluck('count', 'relationship_type');

        // 3. Promise Fulfillment
        $fulfilledCount = (clone $sponsorshipQuery)->where('promise_kept_last_month', true)->count();
        $promiseFulfillmentRate = $totalSponsorships > 0 ? ($fulfilledCount / $totalSponsorships) * 100 : 0;

        // 4. Missed Payments
        $missedPayments = (clone $sponsorshipQuery)->where('promise_kept_last_month', false)->count();

        // 5. Monthly Expenses Covered
        $monthlyExpensesCovered = (clone $sponsorshipQuery)->sum('amount');
        
        // Totals for Elders and Donors
        $totalElders = Elder::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        
        $totalDonors = User::whereHas('sponsorships', function($q) use ($branchId) {
            $q->whereIn('status', ['active', 'pending', 'fulfilled']);
            if ($branchId) {
                $q->whereHas('elder', fn($sq) => $sq->where('branch_id', $branchId));
            }
        })->count();

        // Get recent activity (last 10 activities) - this seems fine as it's limited
        $startDate = Carbon::now()->subDays($dateRange);
        $recentActivity = TimelineEvent::with(['user', 'elder'])
            ->when($branchId, fn($query) => $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId)))
            ->where('created_at', '>=', $startDate)
            ->latest()
            ->take(10)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'description' => $event->description,
                    'time_ago' => $event->created_at->diffForHumans(),
                ];
            });

        return [
            'relationship_distribution' => $relationshipDistribution,
            'promise_fulfillment_rate' => $promiseFulfillmentRate,
            'missed_payments' => $missedPayments,
            'featured_matches' => $this->getFeaturedMatches(),
            'guest_donations_today' => Donation::where('donation_type', '!=', 'pledge')
                ->when($branchId, fn($query) => $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId)))
                ->whereDate('created_at', today())
                ->sum('amount'),
            'monthly_expenses_covered' => $monthlyExpensesCovered,
            'total_sponsorships' => $totalSponsorships,
            'total_elders' => $totalElders,
            'total_donors' => $totalDonors,
            'recent_activity' => $recentActivity,
            'monthly_trend' => $this->monthlyDonationsTrend($branchId, 6),
        ];
    }

    protected function monthlyDonationsTrend($branchId = null, int $months = 6): array
    {
        $months = max(3, min($months, 12));
        $startMonth = now()->subMonths($months - 1)->startOfMonth();
        $endMonth = now()->endOfMonth();
        $baseQuery = Donation::query()
            ->where('status', 'approved')
            ->when($branchId, fn($query) => $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId)));

        $trend = [];
        foreach (CarbonPeriod::create($startMonth, '1 month', $endMonth) as $date) {
            $sum = (clone $baseQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $trend[] = [
                'label' => $date->format('M Y'),
                'amount' => $sum,
            ];
        }

        return $trend;
    }

    protected function donorMonthlyTrend(User $user, int $months = 6): array
    {
        $months = max(3, min($months, 12));
        $startMonth = now()->subMonths($months - 1)->startOfMonth();
        $endMonth = now()->endOfMonth();

        $trend = [];

        foreach (CarbonPeriod::create($startMonth, '1 month', $endMonth) as $date) {
            $amount = Donation::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $trend[] = [
                'label' => $date->format('M Y'),
                'amount' => $amount,
            ];
        }

        return $trend;
    }

    /**
     * Get detailed promise fulfillment data
     */
    public function getPromiseFulfillmentDetails($branchId = null, $dateRange = 30): array
    {
        $query = Sponsorship::query()->whereIn('status', ['active', 'pending', 'fulfilled']);
        if ($branchId) {
            $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $sponsorships = $query->with(['user', 'elder'])->get();

        $fulfilled = $sponsorships->where('promise_kept_last_month', true);
        $missed = $sponsorships->where('promise_kept_last_month', false);

        return [
            'total_sponsorships' => $sponsorships->count(),
            'fulfilled_count' => $fulfilled->count(),
            'missed_count' => $missed->count(),
            'fulfillment_rate' => $sponsorships->count() > 0 ? ($fulfilled->count() / $sponsorships->count()) * 100 : 0,
            'fulfilled_sponsorships' => $fulfilled->map(function($sponsorship) {
                return [
                    'id' => $sponsorship->id,
                    'donor_name' => $sponsorship->user->name,
                    'elder_name' => $sponsorship->elder->name,
                    'amount' => $sponsorship->amount,
                    'relationship' => $sponsorship->relationship_type,
                    'months_supported' => $sponsorship->created_at->diffInMonths(now()),
                ];
            }),
            'missed_sponsorships' => $missed->map(function($sponsorship) {
                return [
                    'id' => $sponsorship->id,
                    'donor_name' => $sponsorship->user->name,
                    'elder_name' => $sponsorship->elder->name,
                    'amount' => $sponsorship->amount,
                    'relationship' => $sponsorship->relationship_type,
                    'last_payment' => $sponsorship->last_payment_date?->format('Y-m-d'),
                ];
            }),
        ];
    }

    /**
     * Get detailed missed payments data
     */
    public function getMissedPaymentsDetails($branchId = null, $dateRange = 30): array
    {
                $query = Sponsorship::query()->whereIn('status', ['active', 'pending', 'fulfilled']);
            // ->where('promise_kept_last_month', false);
        if ($branchId) {
            $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $missedSponsorships = $query->with(['user', 'elder'])->get();

        return [
            'total_missed' => $missedSponsorships->count(),
            'total_amount' => $missedSponsorships->sum('amount'),
            'missed_sponsorships' => $missedSponsorships->map(function($sponsorship) {
                return [
                    'id' => $sponsorship->id,
                    'donor_name' => $sponsorship->user->name,
                    'elder_name' => $sponsorship->elder->name,
                    'amount' => $sponsorship->amount,
                    'relationship' => $sponsorship->relationship_type,
                    'last_payment' => $sponsorship->last_payment_date?->format('Y-m-d'),
                    'days_overdue' => $sponsorship->last_payment_date
                        ? now()->diffInDays($sponsorship->last_payment_date->addMonth())
                        : null,
                ];
            }),
        ];
    }

    /**
     * Get detailed guest donations data
     */
    public function getGuestDonationsDetails($branchId = null, $dateRange = 30): array
    {
        $query = Donation::query()->where('donation_type', '!=', 'pledge');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $donations = $query->orderBy('created_at', 'desc')->get();

        return [
            'total_donations' => $donations->count(),
            'total_amount' => $donations->sum('amount'),
            'average_donation' => $donations->count() > 0 ? $donations->avg('amount') : 0,
            'donations' => $donations->map(function($donation) {
                return [
                    'id' => $donation->id,
                    'amount' => $donation->amount,
                    'donation_type' => $donation->donation_type,
                    'created_at' => $donation->created_at->format('Y-m-d H:i'),
                    'notes' => $donation->notes,
                ];
            }),
        ];
    }

    /**
     * Get detailed monthly expenses data
     */
    public function getMonthlyExpensesDetails($branchId = null, $dateRange = 30): array
    {
        $query = Sponsorship::query()->whereIn('status', ['active', 'pending', 'fulfilled']);
        if ($branchId) {
            $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $sponsorships = $query->with(['user', 'elder'])->get();

        $byRelationship = $sponsorships->groupBy('relationship_type')->map(function($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'average_amount' => $group->avg('amount'),
            ];
        });

        return [
            'total_sponsorships' => $sponsorships->count(),
            'total_monthly_amount' => $sponsorships->sum('amount'),
            'average_pledge_amount' => $sponsorships->count() > 0 ? $sponsorships->avg('amount') : 0,
            'by_relationship' => $byRelationship,
            'top_donors' => $sponsorships->groupBy('user_id')->map(function($group) {
                $user = $group->first()->user;
                return [
                    'name' => $user->name,
                    'total_amount' => $group->sum('amount'),
                    'pledge_count' => $group->count(),
                ];
            })->sortByDesc('total_amount')->take(10)->values(),
        ];
    }

    /**
     * Get activity report data
     */
    public function getActivityReport($branchId = null, $dateRange = 30): array
    {
        $query = TimelineEvent::query()->with(['user', 'elder']);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $activities = $query->orderBy('created_at', 'desc')->get();

        return $activities->map(function($activity) {
            return [
                'id' => $activity->id,
                'description' => $activity->description,
                'event_type' => $activity->event_type,
                'user_name' => $activity->user?->name,
                'elder_name' => $activity->elder?->name,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                'time_ago' => $activity->created_at->diffForHumans(),
            ];
        })->toArray();
    }
}
