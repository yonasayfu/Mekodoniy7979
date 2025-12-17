<?php

namespace App\Services;

use App\Models\DailyStat;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\Pledge;
use App\Models\AnnualReport;
use App\Models\User;
use App\Models\TimelineEvent;
use Carbon\Carbon;

class ReportService
{
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

        $supportedEldersCount = Pledge::where('user_id', $user->id)
            ->where('status', 'active')
            ->distinct('elder_id')
            ->count();

        $data['timeline_events'] = $data['keyTimelineEvents'];
        unset($data['keyTimelineEvents']);

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

        $supportedElders = Elder::whereHas('pledges', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id)
                ->where('status', 'active');
        })->get();

        return array_merge($commonData, ['supportedElders' => $supportedElders]);
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
        $activePledges = Pledge::where('status', 'active')
            ->where('start_date', '<=', now()->subMonth())
            ->get();

        foreach ($activePledges as $pledge) {
            $lastMonthDonations = Donation::where('user_id', $pledge->user_id)
                ->where('elder_id', $pledge->elder_id)
                ->where('created_at', '>=', now()->subMonth())
                ->sum('amount');

            $promiseKept = $lastMonthDonations >= $pledge->amount;

            $pledge->update([
                'promise_kept_last_month' => $promiseKept,
                'consecutive_months_kept' => $promiseKept ?
                    $pledge->consecutive_months_kept + 1 : 0,
                'missed_payment_count' => $promiseKept ?
                    $pledge->missed_payment_count : $pledge->missed_payment_count + 1
            ]);

            if (!$promiseKept) {
                $missedPayments[] = $pledge;
                // TODO: Send notification (SMS/Email)
                // $this->sendPaymentReminder($pledge);
            }
        }

        return $missedPayments;
    }

    /**
     * Generate annual thank you reports
     */
    public function generateAnnualReports(int $year): int
    {
        $consistentDonors = User::whereHas('pledges', function($q) use ($year) {
            $q->where('status', 'active')
              ->where('consecutive_months_kept', '>=', 12);
        })->get();

        $generated = 0;
        foreach ($consistentDonors as $donor) {
            $impactData = $this->calculateAnnualImpact($donor, $year);
            $pdfPath = $this->generateThankYouPDF($donor, $impactData, $year);

            AnnualReport::updateOrCreate(
                ['user_id' => $donor->id, 'report_year' => $year],
                ['impact_data' => $impactData, 'pdf_path' => $pdfPath]
            );
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

        $supportedElders = Pledge::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('elder')
            ->get();

        $timelineEvents = TimelineEvent::where('user_id', $user->id)
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->count();

        return [
            'total_donations' => $totalDonations,
            'supported_elders_count' => $supportedElders->count(),
            'timeline_events_count' => $timelineEvents,
            'supported_elders' => $supportedElders->map(function($pledge) {
                return [
                    'name' => $pledge->elder->first_name . ' ' . $pledge->elder->last_name,
                    'relationship' => $pledge->relationship_type,
                    'months_supported' => $pledge->consecutive_months_kept,
                ];
            }),
        ];
    }

    /**
     * Generate thank you PDF (placeholder)
     */
    private function generateThankYouPDF(User $user, array $impactData, int $year): string
    {
        // TODO: Implement PDF generation
        return "reports/annual/{$user->id}_{$year}.pdf";
    }

    /**
     * Get featured matches for slideshow
     */
    public function getFeaturedMatches(): array
    {
        return Pledge::where('status', 'active')
            ->whereHas('elder', fn($q) => $q->where('is_featured', true))
            ->with(['user', 'elder'])
            ->inRandomOrder()
            ->take(10)
            ->get()
            ->map(function($pledge) {
                return [
                    'donor_name' => $pledge->user->name,
                    'elder_name' => $pledge->elder->first_name . ' ' . $pledge->elder->last_name,
                    'relationship' => $pledge->relationship_type,
                    'months_supported' => $pledge->consecutive_months_kept,
                    'story' => "Supporting {$pledge->elder->first_name} as their {$pledge->relationship_type} for {$pledge->consecutive_months_kept} months"
                ];
            })
            ->toArray();
    }

    /**
     * Get comprehensive admin dashboard data
     */
    public function getEnhancedAdminDashboard($branchId = null): array
    {
        // Include pledges with relevant statuses (active, pending, etc.)
        $query = Pledge::query()->whereIn('status', ['active', 'pending', 'fulfilled']);
        if ($branchId) {
            $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        $pledges = $query->with(['user', 'elder'])->get();

        // Get total counts
        $totalPledges = $pledges->count();
        $totalElders = Elder::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $totalDonors = User::whereHas('pledges', function($q) {
            $q->whereIn('status', ['active', 'pending', 'fulfilled']);
        })->when($branchId, function($q) use ($branchId) {
            $q->whereHas('pledges.elder', fn($sq) => $sq->where('branch_id', $branchId));
        })->count();

        // Get recent activity (last 10 activities)
        $recentActivity = TimelineEvent::with(['user', 'elder'])
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
            'relationship_distribution' => $pledges->groupBy('relationship_type')->map->count(),
            'promise_fulfillment_rate' => $pledges->where('promise_kept_last_month', true)->count() / max($pledges->count(), 1) * 100,
            'missed_payments' => $pledges->where('promise_kept_last_month', false)->count(),
            'featured_matches' => $this->getFeaturedMatches(),
            'guest_donations_today' => Donation::where('donation_type', '!=', 'pledge')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'monthly_expenses_covered' => $pledges->sum('amount'),
            'total_pledges' => $totalPledges,
            'total_elders' => $totalElders,
            'total_donors' => $totalDonors,
            'recent_activity' => $recentActivity
        ];
    }

    /**
     * Get detailed promise fulfillment data
     */
    public function getPromiseFulfillmentDetails($branchId = null, $dateRange = 30): array
    {
        $query = Pledge::query()->whereIn('status', ['active', 'pending', 'fulfilled']);
        if ($branchId) {
            $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $pledges = $query->with(['user', 'elder'])->get();

        $fulfilled = $pledges->where('promise_kept_last_month', true);
        $missed = $pledges->where('promise_kept_last_month', false);

        return [
            'total_pledges' => $pledges->count(),
            'fulfilled_count' => $fulfilled->count(),
            'missed_count' => $missed->count(),
            'fulfillment_rate' => $pledges->count() > 0 ? ($fulfilled->count() / $pledges->count()) * 100 : 0,
            'fulfilled_pledges' => $fulfilled->map(function($pledge) {
                return [
                    'id' => $pledge->id,
                    'donor_name' => $pledge->user->name,
                    'elder_name' => $pledge->elder->name,
                    'amount' => $pledge->amount,
                    'relationship' => $pledge->relationship_type,
                    'months_supported' => $pledge->created_at->diffInMonths(now()),
                ];
            }),
            'missed_pledges' => $missed->map(function($pledge) {
                return [
                    'id' => $pledge->id,
                    'donor_name' => $pledge->user->name,
                    'elder_name' => $pledge->elder->name,
                    'amount' => $pledge->amount,
                    'relationship' => $pledge->relationship_type,
                    'last_payment' => $pledge->last_payment_date?->format('Y-m-d'),
                ];
            }),
        ];
    }

    /**
     * Get detailed missed payments data
     */
    public function getMissedPaymentsDetails($branchId = null, $dateRange = 30): array
    {
        $query = Pledge::query()->whereIn('status', ['active', 'pending', 'fulfilled'])
            ->where('promise_kept_last_month', false);
        if ($branchId) {
            $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $missedPledges = $query->with(['user', 'elder'])->get();

        return [
            'total_missed' => $missedPledges->count(),
            'total_amount' => $missedPledges->sum('amount'),
            'missed_pledges' => $missedPledges->map(function($pledge) {
                return [
                    'id' => $pledge->id,
                    'donor_name' => $pledge->user->name,
                    'elder_name' => $pledge->elder->name,
                    'amount' => $pledge->amount,
                    'relationship' => $pledge->relationship_type,
                    'last_payment' => $pledge->last_payment_date?->format('Y-m-d'),
                    'days_overdue' => $pledge->last_payment_date
                        ? now()->diffInDays($pledge->last_payment_date->addMonth())
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
        $query = Pledge::query()->whereIn('status', ['active', 'pending', 'fulfilled']);
        if ($branchId) {
            $query->whereHas('elder', fn($q) => $q->where('branch_id', $branchId));
        }

        $startDate = Carbon::now()->subDays($dateRange);
        $query->where('created_at', '>=', $startDate);

        $pledges = $query->with(['user', 'elder'])->get();

        $byRelationship = $pledges->groupBy('relationship_type')->map(function($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'average_amount' => $group->avg('amount'),
            ];
        });

        return [
            'total_pledges' => $pledges->count(),
            'total_monthly_amount' => $pledges->sum('amount'),
            'average_pledge_amount' => $pledges->count() > 0 ? $pledges->avg('amount') : 0,
            'by_relationship' => $byRelationship,
            'top_donors' => $pledges->groupBy('user_id')->map(function($group) {
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
