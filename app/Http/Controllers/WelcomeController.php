<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\Pledge;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page with dynamic content.
     */
    public function index(Request $request): Response
    {
        // Fetch data for "Wall of Love"
        $wallOfLove = Pledge::with(['user:id,name', 'elder:id,first_name,last_name,profile_picture_path'])
            ->where('status', 'active')
            ->latest()
            ->take(10) // Get 10 most recent active pledges for the wall of love
            ->get()
            ->map(fn (Pledge $pledge) => [
                'donor_name' => $pledge->user->name,
                'elder_name' => $pledge->elder->first_name . ' ' . $pledge->elder->last_name,
                'elder_id' => $pledge->elder->id,
                'elder_profile_picture' => $pledge->elder->profile_picture_path,
                'pledge_date' => $pledge->created_at->diffForHumans(),
            ]);

        // Fetch data for "Live Counters"
        $totalElders = Elder::count();
        $matchedElders = Pledge::where('status', 'active')->distinct('elder_id')->count();
        $eldersWaiting = $totalElders - $matchedElders; // Basic calculation for now
        $visitsThisMonth = Visit::whereMonth('visit_date', now()->month)
            ->whereYear('visit_date', now()->year)
            ->where('status', 'completed')
            ->count();

        // Fetch data for "Elder Gallery"
        $eldersQuery = Elder::query();

        // Apply filters based on request
        if ($request->has('priority')) {
            $eldersQuery->where('priority_level', $request->input('priority'));
        }
        if ($request->has('gender')) {
            $eldersQuery->where('gender', $request->input('gender'));
        }
        // Add more filters (e.g., region/branch, age) as needed

        $elders = $eldersQuery->select('id', 'first_name', 'last_name', 'profile_picture_path', 'priority_level')
            ->paginate(12) // Paginate results for the gallery
            ->through(fn (Elder $elder) => [
                'id' => $elder->id,
                'full_name' => $elder->first_name . ' ' . $elder->last_name,
                'profile_picture_path' => $elder->profile_picture_path,
                'priority_level' => $elder->priority_level,
            ]);

        return Inertia::render('Welcome', [
            'wallOfLove' => $wallOfLove,
            'liveCounters' => [
                'eldersWaiting' => $eldersWaiting,
                'matchedElders' => $matchedElders,
                'visitsThisMonth' => $visitsThisMonth,
            ],
            'eldersGallery' => $elders,
            'filters' => $request->only(['priority', 'gender']),
        ]);
    }
}
