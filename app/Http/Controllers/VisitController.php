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
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    use HandlesDataExport;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $visits = Visit::with(['user:id,name', 'elder:id,first_name,last_name', 'branch:id,name'])->paginate(10)
            ->through(fn ($visit) => [
                'id' => $visit->id,
                'user_id' => $visit->user_id,
                'user_name' => $visit->user->name,
                'elder_id' => $visit->elder_id,
                'elder_full_name' => $visit->elder->name,
                'branch_id' => $visit->branch_id,
                'branch_name' => $visit->branch->name,
                'visit_date' => $visit->visit_date,
                'purpose' => $visit->purpose,
                'notes' => $visit->notes,
                'status' => $visit->status,
            ]);
        return Inertia::render('Visits/Index', [
            'visits' => $visits,
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
            'Visit scheduled for ' . $visit->elder->name . ' by ' . $visit->user->name . ' on ' . $visit->visit_date->format('Y-m-d H:i'),
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
        $visit->load('user', 'elder', 'branch', 'activityLogs.causer');

        return Inertia::render('Visits/Show', [
            'visit' => $visit,
            'activity' => $visit->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Visits',
                    'url' => route('visits.index'),
                ],
                [
                    'label' => $visit->user->name . ' - ' . $visit->elder->name,
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit): Response
    {
        $visit->load('user', 'elder', 'branch', 'activityLogs.causer');

        $users = User::all(['id', 'name']);
        $elders = Elder::all(['id', 'first_name', 'last_name']);
        $branches = Branch::all(['id', 'name']);
        return Inertia::render('Visits/Edit', [
            'visit' => $visit,
            'users' => $users,
            'elders' => $elders,
            'branches' => $branches,
            'activity' => $visit->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Visits',
                    'url' => route('visits.index'),
                ],
                [
                    'label' => $visit->user->name . ' - ' . $visit->elder->name,
                    'url' => route('visits.show', $visit),
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
    public function update(UpdateVisitRequest $request, Visit $visit, TimelineEventService $timelineEventService)
    {
        $visit->update($request->validated());

        // Create timeline event for visit update
        $timelineEventService->createEvent(
            'visit_updated',
            'Visit for ' . $visit->elder->name . ' by ' . $visit->user->name . ' updated to status: ' . $visit->status,
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

    public function export(Request $request)
    {
        return $this->handleExport($request, Visit::class, ExportConfig::visits(), [
            'label' => 'Visits Directory',
            'type' => 'visits',
        ]);
    }
}