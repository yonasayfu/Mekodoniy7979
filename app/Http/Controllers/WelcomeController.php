<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\Sponsorship;
use App\Services\CountersService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page with dynamic content.
     */
    public function index(Request $request, CountersService $countersService): Response
    {
        // Fetch data for "Wall of Love"
        $wallOfLove = Sponsorship::with(['user:id,name', 'elder:id,first_name,last_name,profile_picture_path'])
            ->where('status', 'active')
            ->latest()
            ->take(10) // Get 10 most recent active sponsorships for the wall of love
            ->get()
            ->map(fn (Sponsorship $sponsorship) => [
                'donor_name' => $sponsorship->user->name,
                'elder_name' => $sponsorship->elder->first_name . ' ' . $sponsorship->elder->last_name,
                'elder_id' => $sponsorship->elder->id,
                'elder_profile_picture' => $sponsorship->elder->profile_picture_path,
                'sponsorship_date' => $sponsorship->created_at->diffForHumans(),
            ]);

        $liveCounters = $countersService->getWelcomeCounters();

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

        $heroSlides = \App\Models\HeroSlide::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(fn ($slide) => [
                'title' => $slide->title,
                'description' => $slide->description,
                'image' => $slide->image,
                'cta_text' => $slide->cta_text,
                'cta_link' => $slide->cta_link,
            ]);

        return Inertia::render('Welcome', [
            'wallOfLove' => $wallOfLove,
            'liveCounters' => [
                'eldersWaiting' => $liveCounters['eldersWaiting'] ?? 0,
                'matchedElders' => $liveCounters['matchedElders'] ?? 0,
                'visitsThisMonth' => $liveCounters['visitsThisMonth'] ?? 0,
            ],
            'eldersGallery' => $elders,
            'filters' => $request->only(['priority', 'gender']),
            'heroSlides' => $heroSlides,
        ]);
    }
}
