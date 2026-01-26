<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\Sponsorship;
use App\Services\CountersService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                'donor_name' => optional($sponsorship->user)->name ?? 'Community donor',
                'elder_name' => $sponsorship->elder->first_name . ' ' . $sponsorship->elder->last_name,
                'elder_id' => $sponsorship->elder->id,
                'elder_profile_photo_url' => $sponsorship->elder->profile_photo_url,
                'sponsorship_date' => $sponsorship->created_at->diffForHumans(),
            ]);

        $liveCounters = $countersService->getWelcomeCounters();

        // Fetch data for "Elder Gallery"
        $eldersQuery = Elder::query();

        $includeFunded = $request->boolean('include_funded');
        if (! $includeFunded) {
            $eldersQuery->where(function ($query) {
                $query->where('funding_goal', 0)
                    ->orWhereColumn('funding_received', '<', 'funding_goal');
            });
        }

        // Apply filters based on request
        if ($request->has('priority')) {
            $eldersQuery->where('priority_level', $request->input('priority'));
        }
        if ($request->has('gender')) {
            $eldersQuery->where('gender', $request->input('gender'));
        }
        if ($request->has('relationship')) {
            $eldersQuery->where('relationship_type', $request->input('relationship'));
        }
        // Add more filters (e.g., region/branch, age) as needed

        $elders = $eldersQuery
            ->with('branch:id,name')
            ->select(
                'id',
                'first_name',
                'last_name',
                'profile_picture_path',
                'priority_level',
                'relationship_type',
                'branch_id',
                'city',
                'country',
            )
            ->paginate(12) // Paginate results for the gallery
            ->through(fn (Elder $elder) => [
                'id' => $elder->id,
                'full_name' => $elder->first_name . ' ' . $elder->last_name,
                'profile_photo_url' => $elder->profile_photo_url,
                'priority_level' => $elder->priority_level,
                'relationship_type' => $elder->relationship_type,
                'branch_name' => optional($elder->branch)->name,
                'location' => $elder->city && $elder->country
                    ? "{$elder->city}, {$elder->country}"
                    : $elder->country,
                'funding_goal' => $elder->funding_goal,
                'funding_received' => $elder->funding_received,
                'funding_progress' => $elder->funding_goal
                    ? min($elder->funding_received / $elder->funding_goal, 1)
                    : 0,
                'funding_needed' => max($elder->funding_goal - $elder->funding_received, 0),
                'is_funded' => $elder->funding_goal > 0
                    && $elder->funding_received >= $elder->funding_goal,
            ]);

        $heroSlides = \App\Models\HeroSlide::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(fn ($slide) => [
                'title' => $slide->title,
                'description' => $slide->description,
                'image' => $this->resolveHeroImage($slide->image),
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
            'filters' => array_merge(
                $request->only(['priority', 'gender', 'relationship']),
                ['include_funded' => $includeFunded],
            ),
            'heroSlides' => $heroSlides,
        ]);
    }

    private function resolveHeroImage(?string $path): string
    {
        if (! $path) {
            return asset('images/hero-father.svg');
        }

        if (Str::contains($path, 'placeholder.com')) {
            return asset('images/hero-father.svg');
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}
