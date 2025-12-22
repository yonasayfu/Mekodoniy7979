<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\Sponsorship;
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

        // Fetch data for "Live Counters"
        $totalElders = Elder::count();
        $matchedElders = Sponsorship::where('status', 'active')->distinct('elder_id')->count();
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

        $heroSlides = [
            [
                'title' => 'Become a Father Today',
                'description' => 'Embrace an elder with the honor and respect of a father figure. Your support provides more than just essentials; it provides dignity.',
                'image' => 'https://via.placeholder.com/1200x400.png/3498db/ffffff?text=Photo+of+a+Dignified+Elderly+Man',
                'cta_text' => 'Find a Father',
                'cta_link' => '#elders-gallery',
            ],
            [
                'title' => 'Become a Mother Today',
                'description' => 'Extend your family by supporting an elderly woman. Your sponsorship is a promise of care, comfort, and companionship.',
                'image' => 'https://via.placeholder.com/1200x400.png/e74c3c/ffffff?text=Photo+of+a+Kind+Elderly+Woman',
                'cta_text' => 'Find a Mother',
                'cta_link' => '#elders-gallery',
            ],
            [
                'title' => 'Support a Brother or Sister',
                'description' => 'Help a younger resident in need. Your contribution can fund education, health, and a brighter future.',
                'image' => 'https://via.placeholder.com/1200x400.png/2ecc71/ffffff?text=Photo+of+a+Hopeful+Youth',
                'cta_text' => 'Support a Sibling',
                'cta_link' => '#elders-gallery',
            ],
            [
                'title' => 'Give the Gift of Family',
                'description' => 'A small, one-time donation can provide a festive meal, warm clothing, or essential medicine. Make an immediate impact.',
                'image' => 'https://via.placeholder.com/1200x400.png/f1c40f/000000?text=Donate+for+Immediate+Needs',
                'cta_text' => 'Donate a Meal',
                'cta_link' => '#guest-donation-form',
            ],
        ];

        return Inertia::render('Welcome', [
            'wallOfLove' => $wallOfLove,
            'liveCounters' => [
                'eldersWaiting' => $eldersWaiting,
                'matchedElders' => $matchedElders,
                'visitsThisMonth' => $visitsThisMonth,
            ],
            'eldersGallery' => $elders,
            'filters' => $request->only(['priority', 'gender']),
            'heroSlides' => $heroSlides,
        ]);
    }
}
