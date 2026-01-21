<?php

namespace App\Http\Controllers;

use App\Models\AnnualReport;
use App\Models\Elder;
use App\Models\SponsorshipProposal;
use App\Models\TimelineEvent;
use App\Models\User;
use App\Services\CountersService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DonorDashboardController extends Controller
{
    /**
     * Display the donor dashboard.
     */
    public function index(CountersService $countersService): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->donorProfile?->is_completed) {
            return redirect()->route('donors.onboarding.show');
        }

        $donorCounters = $countersService->getDonorCounters($user);

        $totalDonations = (float) ($donorCounters['total_donations'] ?? 0);
        $eldersSupported = (int) ($donorCounters['elders_supported'] ?? 0);
        $lastDonationHuman = $donorCounters['last_donation_human'] ?? null;

        $metrics = [
            ['label' => 'Total Donations', 'value' => number_format($totalDonations, 2) . ' ETB', 'icon' => 'Gift'],
            ['label' => 'Elders Supported', 'value' => $eldersSupported, 'icon' => 'HeartHandshake'],
            ['label' => 'Last Donation', 'value' => $lastDonationHuman ?: 'N/A', 'icon' => 'CalendarCheck'],
        ];

        // My Elders Section (simplified for now)
        $myElders = Elder::whereHas('donations', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'completed');
        })
            ->select('id', 'first_name', 'last_name', 'profile_picture_path', 'priority_level')
            ->get()
            ->map(fn (Elder $elder) => [
                'id' => $elder->id,
                'first_name' => $elder->first_name,
                'last_name' => $elder->last_name,
                'priority_level' => $elder->priority_level,
                'profile_photo_url' => $elder->profile_photo_url,
            ]);

        // Fetch timeline events for the authenticated user
        $timelineEvents = TimelineEvent::where('user_id', $user->id)
            ->with(['user', 'elder'])
            ->latest('occurred_at')
            ->take(10) // Limit to 10 recent events
            ->get();

        $pendingProposals = SponsorshipProposal::with(['elder:id,first_name,last_name,profile_picture_path,priority_level'])
            ->where('donor_id', $user->id)
            ->where('status', SponsorshipProposal::STATUS_PENDING)
            ->orderBy('expires_at')
            ->get()
            ->map(fn (SponsorshipProposal $proposal) => [
                'id' => $proposal->id,
                'elder' => $proposal->elder ? [
                    'id' => $proposal->elder->id,
                    'first_name' => $proposal->elder->first_name,
                    'last_name' => $proposal->elder->last_name,
                    'priority_level' => $proposal->elder->priority_level,
                    'profile_photo_url' => $proposal->elder->profile_photo_url,
                ] : null,
                'amount' => $proposal->amount,
                'frequency' => $proposal->frequency,
                'relationship_type' => $proposal->relationship_type,
                'notes' => $proposal->notes,
                'expires_at' => optional($proposal->expires_at)->toIso8601String(),
                'expires_at_human' => optional($proposal->expires_at)->diffForHumans(),
            ]);

        $annualReports = AnnualReport::where('user_id', $user->id)
            ->orderBy('report_year', 'desc')
            ->get()
            ->map(fn (AnnualReport $report) => [
                'id' => $report->id,
                'year' => $report->report_year,
                'download_url' => route('annual-reports.download', $report),
                'generated_at' => $report->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('DonorDashboard', [
            'metrics' => $metrics,
            'myElders' => $myElders,
            'timelineEvents' => $timelineEvents,
            'pendingProposals' => $pendingProposals,
            'annualReports' => $annualReports,
        ]);
    }
}
