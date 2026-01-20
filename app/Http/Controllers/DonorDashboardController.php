<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Services\CountersService;
use App\Models\TimelineEvent;
use App\Models\User;
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

        return Inertia::render('DonorDashboard', [
            'metrics' => $metrics,
            'myElders' => $myElders,
            'timelineEvents' => $timelineEvents,
        ]);
    }
}
