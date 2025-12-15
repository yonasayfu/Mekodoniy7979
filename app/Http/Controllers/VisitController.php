<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Visit;
use App\Models\User;
use App\Models\Elder;
use App\Models\Branch;
use App\Support\Services\TimelineEventService;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $visits = Visit::with(['user', 'elder', 'branch'])->paginate(10);
        return Inertia::render('Visits/Index', [
            'visits' => $visits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $users = User::all(['id', 'name']);
        $elders = Elder::all(['id', 'first_name', 'last_name']);
        $branches = Branch::all(['id', 'name']);
        return Inertia::render('Visits/Create', [
            'users' => $users,
            'elders' => $elders,
            'branches' => $branches,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisitRequest $request, TimelineEventService $timelineEventService)
    {
        $visit = Visit::create($request->validated());

        // Create timeline event for visit creation
        $timelineEventService->createEvent(
            'visit_created',
            'Visit scheduled for ' . $visit->elder->first_name . ' ' . $visit->elder->last_name . ' by ' . $visit->user->name . ' on ' . $visit->visit_date->format('Y-m-d H:i'),
            Carbon::now(),
            $visit->user,
            $visit->elder,
            null
        );

        return redirect()->route('visits.index')->with('success', 'Visit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit): Response
    {
        return Inertia::render('Visits/Show', [
            'visit' => $visit->load(['user', 'elder', 'branch']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit): Response
    {
        $users = User::all(['id', 'name']);
        $elders = Elder::all(['id', 'first_name', 'last_name']);
        $branches = Branch::all(['id', 'name']);
        return Inertia::render('Visits/Edit', [
            'visit' => $visit->load(['user', 'elder', 'branch']),
            'users' => $users,
            'elders' => $elders,
            'branches' => $branches,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVisitRequest $request, Visit $visit, TimelineEventService $timelineEventService)
    {
        $visit->update($request->validated());

        // Create timeline event for visit update
        $timelineEventService->createEvent(
            'visit_updated',
            'Visit for ' . $visit->elder->first_name . ' ' . $visit->elder->last_name . ' by ' . $visit->user->name . ' updated to status: ' . $visit->status,
            Carbon::now(),
            $visit->user,
            $visit->elder,
            null
        );

        return redirect()->route('visits.index')->with('success', 'Visit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        $visit->delete();
        return redirect()->route('visits.index')->with('success', 'Visit deleted successfully.');
    }
}