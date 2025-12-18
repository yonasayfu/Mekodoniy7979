<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorshipRequest;
use App\Http\Requests\UpdateSponsorshipRequest;
use App\Models\Sponsorship;
use App\Models\User;
use App\Models\Elder;
use App\Support\Services\TimelineEventService;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Http\Request;

class SponsorshipController extends Controller
{
    use HandlesDataExport;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $sponsorships = Sponsorship::with(['user:id,name', 'elder:id,first_name,last_name'])->paginate(10)
            ->through(fn ($sponsorship) => [
                'id' => $sponsorship->id,
                'user_id' => $sponsorship->user_id,
                'user_name' => $sponsorship->user->name,
                'elder_id' => $sponsorship->elder_id,
                'elder_full_name' => $sponsorship->elder->name,
                'amount' => $sponsorship->amount,
                'frequency' => $sponsorship->frequency,
                'start_date' => $sponsorship->start_date,
                'end_date' => $sponsorship->end_date,
                'status' => $sponsorship->status,
                'notes' => $sponsorship->notes,
            ]);
        return Inertia::render('Sponsorships/Index', [
            'sponsorships' => $sponsorships,
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
        return Inertia::render('Sponsorships/Create', [
            'users' => $users,
            'elders' => $elders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorshipRequest $request, TimelineEventService $timelineEventService)
    {
        $sponsorship = Sponsorship::create($request->validated());

        $timelineEventService->create(
            $sponsorship,
            'Sponsorship created',
            'A new sponsorship has been created.',
            $sponsorship->user,
            $sponsorship->elder,
            [
                'amount' => $sponsorship->amount,
                'frequency' => $sponsorship->frequency,
                'start_date' => $sponsorship->start_date,
                'end_date' => $sponsorship->end_date,
            ]
        );

        return redirect()->route('sponsorships.index')->with('success', 'Sponsorship created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sponsorship $sponsorship): Response
    {
        $sponsorship->load(['user', 'elder' => fn ($query) => $query->withoutGlobalScope(\App\Scopes\BranchScope::class), 'activityLogs.causer']);
        $sponsorship->refresh();

        return Inertia::render('Sponsorships/Show', [
            'sponsorship' => $sponsorship,
            'activity' => $sponsorship->activityLogs,
            'breadcrumbs' => [
                [
                    'title' => 'Sponsorships',
                    'href' => route('sponsorships.index'),
                ],
                [
                    'title' => ($sponsorship->user->name ?? 'Unknown User') . ' - ' . ($sponsorship->elder->name ?? 'Unknown Elder'),
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sponsorship $sponsorship): Response
    {
        $sponsorship->load('user', 'elder', 'activityLogs.causer');

        $users = User::all(['id', 'name']);
        $elders = Elder::all(['id', 'first_name', 'last_name']);
        return Inertia::render('Sponsorships/Edit', [
            'sponsorship' => $sponsorship,
            'users' => $users,
            'elders' => $elders,
            'activity' => $sponsorship->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Sponsorships',
                    'url' => route('sponsorships.index'),
                ],
                [
                    'label' => ($sponsorship->user->name ?? 'Unknown User') . ' - ' . ($sponsorship->elder->name ?? 'Unknown Elder'),
                    'url' => route('sponsorships.show', $sponsorship),
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
    public function update(UpdateSponsorshipRequest $request, Sponsorship $sponsorship, TimelineEventService $timelineEventService)
    {
        $sponsorship->update($request->validated());

        $timelineEventService->create(
            $sponsorship,
            'Sponsorship updated',
            'The sponsorship has been updated.',
            $sponsorship->user,
            $sponsorship->elder,
            $request->validated()
        );

        return redirect()->route('sponsorships.index')->with('success', 'Sponsorship updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sponsorship $sponsorship)
    {
        $sponsorship->delete();
        return redirect()->route('sponsorships.index')->with('success', 'Sponsorship deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Sponsorship::class, ExportConfig::sponsorships(), [
            'label' => 'Sponsorships Directory',
            'type' => 'sponsorships',
        ]);
    }
}
