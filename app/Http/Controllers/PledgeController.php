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
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Http\Request;


class PledgeController extends Controller
{
    use HandlesDataExport;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $pledges = Pledge::with(['user:id,name', 'elder:id,first_name,last_name'])->paginate(10)
            ->through(fn ($pledge) => [
                'id' => $pledge->id,
                'user_id' => $pledge->user_id,
                'user_name' => $pledge->user->name,
                'elder_id' => $pledge->elder_id,
                'elder_full_name' => $pledge->elder->name,
                'amount' => $pledge->amount,
                'frequency' => $pledge->frequency,
                'start_date' => $pledge->start_date,
                'end_date' => $pledge->end_date,
                'status' => $pledge->status,
                'notes' => $pledge->notes,
            ]);
        return Inertia::render('Pledges/Index', [
            'pledges' => $pledges,
            'can' => [
                'create' => true,
                'edit' => true,
                'delete' => true,
            ]
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
            'Pledge created for ' . $pledge->elder->name . ' by ' . $pledge->user->name . ' for ' . ($pledge->amount ? $pledge->amount . ' ETB ' : '') . ($pledge->frequency ? '(' . $pledge->frequency . ')' : ''),
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
        $pledge->load('user', 'elder', 'activityLogs.causer');

        return Inertia::render('Pledges/Show', [
            'pledge' => $pledge,
            'activity' => $pledge->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Pledges',
                    'url' => route('pledges.index'),
                ],
                [
                    'label' => $pledge->user->name . ' - ' . $pledge->elder->name,
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pledge $pledge): Response
    {
        $pledge->load('user', 'elder', 'activityLogs.causer');

        $users = User::all(['id', 'name']);
        $elders = Elder::all(['id', 'first_name', 'last_name']);
        return Inertia::render('Pledges/Edit', [
            'pledge' => $pledge,
            'users' => $users,
            'elders' => $elders,
            'activity' => $pledge->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Pledges',
                    'url' => route('pledges.index'),
                ],
                [
                    'label' => $pledge->user->name . ' - ' . $pledge->elder->name,
                    'url' => route('pledges.show', $pledge),
                ],
                [
                    'label' => 'Edit',
                ],
            ],
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
            'Pledge for ' . $pledge->elder->name . ' by ' . $pledge->user->name . ' updated to status: ' . $pledge->status,
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

    public function export(Request $request)
    {
        return $this->handleExport($request, Pledge::class, ExportConfig::pledges(), [
            'label' => 'Pledges Directory',
            'type' => 'pledges',
        ]);
    }
}