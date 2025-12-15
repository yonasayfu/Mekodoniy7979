<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePledgeRequest;
use App\Http\Requests\UpdatePledgeRequest;
use App\Models\Pledge;
use App\Models\User;
use App\Models\Elder;
use App\Support\Services\TimelineEventService; // Import TimelineEventService
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon; // Import Carbon for occurred_at

class PledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $pledges = Pledge::with(['user', 'elder'])->paginate(10);
        return Inertia::render('Pledges/Index', [
            'pledges' => $pledges,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $users = User::all(['id', 'name']);
        $elders = Elder::all(['id', 'first_name', 'last_name']);
        return Inertia::render('Pledges/Create', [
            'users' => $users,
            'elders' => $elders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePledgeRequest $request, TimelineEventService $timelineEventService)
    {
        $pledge = Pledge::create($request->validated());

        // Create timeline event for pledge creation
        $timelineEventService->createEvent(
            'pledge_created',
            'Pledge created for ' . $pledge->elder->first_name . ' ' . $pledge->elder->last_name . ' by ' . $pledge->user->name . ' for ' . ($pledge->amount ? $pledge->amount . ' ETB ' : '') . ($pledge->frequency ? '(' . $pledge->frequency . ')' : ''),
            Carbon::now(),
            $pledge->user,
            $pledge->elder,
            null
        );

        return redirect()->route('pledges.index')->with('success', 'Pledge created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pledge $pledge): Response
    {
        return Inertia::render('Pledges/Show', [
            'pledge' => $pledge->load(['user', 'elder']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pledge $pledge): Response
    {
        $users = User::all(['id', 'name']);
        $elders = Elder::all(['id', 'first_name', 'last_name']);
        return Inertia::render('Pledges/Edit', [
            'pledge' => $pledge->load(['user', 'elder']),
            'users' => $users,
            'elders' => $elders,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePledgeRequest $request, Pledge $pledge, TimelineEventService $timelineEventService)
    {
        $pledge->update($request->validated());

        // Create timeline event for pledge update
        $timelineEventService->createEvent(
            'pledge_updated',
            'Pledge for ' . $pledge->elder->first_name . ' ' . $pledge->elder->last_name . ' by ' . $pledge->user->name . ' updated to status: ' . $pledge->status,
            Carbon::now(),
            $pledge->user,
            $pledge->elder,
            null
        );

        return redirect()->route('pledges.index')->with('success', 'Pledge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pledge $pledge)
    {
        $pledge->delete();
        return redirect()->route('pledges.index')->with('success', 'Pledge deleted successfully.');
    }
}