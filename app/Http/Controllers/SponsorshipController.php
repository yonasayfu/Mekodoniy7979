<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorshipRequest;
use App\Http\Requests\UnmatchSponsorshipRequest;
use App\Http\Requests\UpdateSponsorshipRequest;
use App\Models\PreSponsorship;
use App\Models\Sponsorship;
use App\Models\User;
use App\Models\Elder;
use App\Notifications\SponsorshipUnmatchedNotification;
use App\Support\Services\ElderMatchStateService;
use App\Support\Services\TimelineEventService;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SponsorshipController extends Controller
{
    use HandlesDataExport;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Sponsorship::with(['user:id,name', 'elder:id,first_name,last_name']);

        if ($search = trim((string) $request->query('search', ''))) {
            $lowerSearch = mb_strtolower($search);
            $query->where(function ($builder) use ($lowerSearch) {
                $builder->whereRaw('LOWER(notes) LIKE ?', ["%{$lowerSearch}%"])
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->whereRaw('LOWER(name) LIKE ?', ["%{$lowerSearch}%"]))
                    ->orWhereHas('elder', fn ($elderQuery) => $elderQuery->whereRaw('LOWER(CONCAT(first_name, \' \', last_name)) LIKE ?', ["%{$lowerSearch}%"]));
            });
        }

        $whitelistedSorts = [
            'amount' => 'amount',
            'status' => 'status',
            'created_at' => 'created_at',
        ];
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        if (isset($whitelistedSorts[$sort])) {
            $query->orderBy($whitelistedSorts[$sort], $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        $sponsorships = $query->paginate(max(1, min(100, $perPage)))
            ->through(fn ($sponsorship) => [
                'id' => $sponsorship->id,
                'user_id' => $sponsorship->user_id,
                'user_name' => optional($sponsorship->user)->name ?? 'Unknown donor',
                'elder_id' => $sponsorship->elder_id,
                'elder_full_name' => optional($sponsorship->elder)->name,
                'amount' => $sponsorship->amount,
                'frequency' => $sponsorship->frequency,
                'start_date' => $sponsorship->start_date,
                'end_date' => $sponsorship->end_date,
                'status' => $sponsorship->status,
                'notes' => $sponsorship->notes,
            ]);

        $pendingPledges = PreSponsorship::pending()
            ->with(['elder:id,first_name,last_name', 'donation:id,donation_type,status,amount,currency'])
            ->orderByDesc('created_at')
            ->take(6)
            ->get()
            ->map(fn (PreSponsorship $pledge) => [
                'id' => $pledge->id,
                'name' => $pledge->name,
                'email' => $pledge->email,
                'phone' => $pledge->phone,
                'relationship_type' => $pledge->relationship_type,
                'amount' => $pledge->amount,
                'currency' => $pledge->currency,
                'status' => $pledge->status,
                'elder_id' => $pledge->elder_id,
                'elder_name' => optional($pledge->elder)->first_name ? ($pledge->elder->first_name . ' ' . $pledge->elder->last_name) : null,
                'branch_id' => $pledge->branch_id,
                'created_at' => $pledge->created_at ? $pledge->created_at->toDateTimeString() : null,
                'donation_id' => $pledge->donation_id,
                'donation_amount' => $pledge->donation?->amount,
                'donation_currency' => $pledge->donation?->currency,
            ]);

        return Inertia::render('Sponsorships/Index', [
            'sponsorships' => $sponsorships,
            'can' => [
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
            'preSponsorships' => $pendingPledges,
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
        $sponsorship->loadMissing(['user', 'elder']);

        $timelineEventService->createEvent(
            'sponsorship_created',
            sprintf(
                'Sponsorship for %s created by %s. Amount: %s %s (%s).',
                optional($sponsorship->elder)->name ?? 'an elder',
                optional($sponsorship->user)->name ?? 'a donor',
                number_format((float) $sponsorship->amount, 2),
                $sponsorship->currency ?? 'ETB',
                $sponsorship->frequency ?? 'unspecified frequency'
            ),
            Carbon::now(),
            $sponsorship->user,
            $sponsorship->elder
        );

        return redirect()->route('sponsorships.index')->with('success', 'Sponsorship created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sponsorship $sponsorship): Response
    {
        $sponsorship->load(['user', 'elder' => fn ($query) => $query->withoutGlobalScope(\App\Scopes\BranchScope::class), 'activityLogs.causer']);

        return Inertia::render('Sponsorships/Show', [
            'sponsorship' => [
                'id' => $sponsorship->id,
                'user_id' => $sponsorship->user_id,
                'user' => $sponsorship->user ? [
                    'id' => $sponsorship->user->id,
                    'name' => $sponsorship->user->name,
                ] : null,
                'elder_id' => $sponsorship->elder_id,
                'elder' => $sponsorship->elder ? [
                    'id' => $sponsorship->elder->id,
                    'first_name' => $sponsorship->elder->first_name,
                    'last_name' => $sponsorship->elder->last_name,
                ] : null,
                'amount' => $sponsorship->amount,
                'frequency' => $sponsorship->frequency,
                'start_date' => $sponsorship->start_date,
                'end_date' => $sponsorship->end_date,
                'status' => $sponsorship->status,
                'notes' => $sponsorship->notes,
            ],
            'activity' => $sponsorship->activityLogs,
            'can' => [
                'unmatch' => auth()->user()->can('sponsorships.manage'),
            ],
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
        $sponsorship->loadMissing(['user', 'elder']);

        $timelineEventService->createEvent(
            'sponsorship_updated',
            sprintf(
                'Sponsorship for %s updated by %s. Amount: %s %s (%s).',
                optional($sponsorship->elder)->name ?? 'an elder',
                optional($sponsorship->user)->name ?? 'a donor',
                number_format((float) $sponsorship->amount, 2),
                $sponsorship->currency ?? 'ETB',
                $sponsorship->frequency ?? 'unspecified frequency'
            ),
            Carbon::now(),
            $sponsorship->user,
            $sponsorship->elder
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

    public function unmatch(
        UnmatchSponsorshipRequest $request,
        Sponsorship $sponsorship,
        TimelineEventService $timelineEventService,
        ElderMatchStateService $matchStateService
    ) {
        if ($sponsorship->status === 'cancelled') {
            return back()->with('info', 'This sponsorship has already been closed.');
        }

        $sponsorship->loadMissing(['elder', 'user']);

        $sponsorship->update([
            'status' => 'cancelled',
            'end_date' => now(),
        ]);

        if ($sponsorship->elder) {
            $matchStateService->sync($sponsorship->elder);

            $timelineEventService->createEvent(
                'sponsorship_unmatched',
                'Sponsorship closed by '.$request->user()->name,
                Carbon::now(),
                $request->user(),
                $sponsorship->elder
            );
        }

        if ($sponsorship->user) {
            $sponsorship->user->notify(new SponsorshipUnmatchedNotification(
                $sponsorship,
                $request->input('reason')
            ));
        }

        return back()->with('success', 'Sponsorship closed and elder returned to the pool.');
    }
}
