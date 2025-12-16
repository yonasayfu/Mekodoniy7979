<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Elder;
use App\Models\Pledge;
use App\Models\TimelineEvent;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF Facade
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        // For now, just a placeholder list of reports
        $reports = [
            ['name' => 'Donations by Branch', 'description' => 'Summary of donations per branch.'],
            ['name' => 'Elders by Priority', 'description' => 'List of elders grouped by priority level.'],
            ['name' => 'Impact Book (Donor)', 'description' => 'Personalized report for donors.', 'route' => route('reports.impact-book')],
        ];

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
        ]);
    }

    /**
     * Generate a personalized "Impact Book" PDF for the authenticated donor.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateImpactBook(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Ensure only authenticated users (donors) can generate their impact book
        if (!$user || !$user->hasRole('External')) { // Assuming 'External' role for donors
            abort(403, 'Unauthorized to generate Impact Book.');
        }

        // Fetch data for the Impact Book
        $totalDonations = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $supportedElders = Elder::whereHas('pledges', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'active');
        })->get();

        $keyTimelineEvents = TimelineEvent::where('user_id', $user->id)
            ->with('elder')
            ->latest('occurred_at')
            ->take(10) // Get top 10 recent events
            ->get();

        // Pass data to a Blade view for PDF generation
        $pdf = Pdf::loadView('reports.impact_book', compact('user', 'totalDonations', 'supportedElders', 'keyTimelineEvents'));

        // You can customize the PDF filename
        $filename = 'Impact_Book_' . $user->name . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}