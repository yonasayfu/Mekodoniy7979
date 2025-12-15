<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
            // Add more reports as needed
        ];

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
        ]);
    }
}
