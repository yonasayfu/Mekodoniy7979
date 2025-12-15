<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Elder;
use App\Models\User;
use App\Models\TimelineEvent; // Import TimelineEvent
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DonorDashboardController extends Controller
{
    /**
     * Display the donor dashboard.
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        // Metrics
        $totalDonations = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $eldersSupported = Elder::whereHas('donations', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'completed');
        })->count();

        $lastDonation = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        $metrics = [
            ['label' => 'Total Donations', 'value' => number_format($totalDonations, 2) . ' ETB', 'icon' => 'Gift'],
            ['label' => 'Elders Supported', 'value' => $eldersSupported, 'icon' => 'HeartHandshake'],
            ['label' => 'Last Donation', 'value' => $lastDonation ? $lastDonation->created_at->diffForHumans() : 'N/A', 'icon' => 'CalendarCheck'],
        ];

        // My Elders Section (simplified for now)
        $myElders = Elder::whereHas('donations', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'completed');
        })
            ->select('id', 'first_name', 'last_name', 'profile_picture_path', 'priority_level')
            ->get();

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