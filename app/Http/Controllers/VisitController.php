<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Branch;
use App\Models\Elder;
use App\Models\User;
use App\Models\Visit;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use App\Support\Services\TimelineEventService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VisitController extends Controller
{
    use HandlesDataExport;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(5, min($perPage, 100));
        $sort = $request->input('sort', 'visit_date');
        $direction = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $visitsQuery = Visit::query()
            ->with(['user:id,name', 'elder:id,first_name,last_name', 'branch:id,name'])
            ->select('visits.*');

        if ($search = trim((string) $request->input('search'))) {
            $needle = mb_strtolower($search);
            $visitsQuery->where(function ($query) use ($needle) {
                $query->whereRaw('LOWER(purpose) LIKE ?', ['%'.$needle.'%'])
                    ->orWhereHas('user', function ($userQuery) use ($needle) {
                        $userQuery->whereRaw('LOWER(name) LIKE ?', ['%'.$needle.'%']);
                    })
                    ->orWhereHas('elder', function ($elderQuery) use ($needle) {
                        $elderQuery->whereRaw('LOWER(first_name) LIKE ?', ['%'.$needle.'%'])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', ['%'.$needle.'%']);
                    });
            });
        }

        if ($sort === 'user_name') {
            $visitsQuery
                ->join('users as sort_users', 'sort_users.id', '=', 'visits.user_id')
                ->orderBy('sort_users.name', $direction);
        } elseif ($sort === 'elder_full_name') {
            $visitsQuery
                ->join('elders as sort_elders', 'sort_elders.id', '=', 'visits.elder_id')
                ->orderBy('sort_elders.first_name', $direction)
                ->orderBy('sort_elders.last_name', $direction);
        } else {
            $visitsQuery->orderBy('visit_date', $direction);
        }

        $visits = $visitsQuery->paginate($perPage)->withQueryString()
            ->through(fn (Visit $visit) => [
                'id' => $visit->id,
                'user_id' => $visit->user_id,
                'user_name' => $visit->user->name,
                'elder_id' => $visit->elder_id,
                'elder_full_name' => $visit->elder->name,
                'branch_id' => $visit->branch_id,
                'branch_name' => $visit->branch->name,
                'visit_date' => optional($visit->visit_date)?->toIso8601String(),
                'purpose' => $visit->purpose,
                'notes' => $visit->notes,
                'status' => $visit->status,
                'needs_translator' => $visit->needs_translator,
                'needs_transport' => $visit->needs_transport,
                'approval_deadline' => optional($visit->approval_deadline)?->toIso8601String(),
            ]);

        return Inertia::render('Visits/Index', [
            'visits' => $visits,
            'stats' => $this->visitStats(),
            'calendar' => $this->calendarPayload($request),
            'slaAlerts' => $this->slaAlerts(),
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'month']),
            'can' => [
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
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
        $payload = $this->hydrateLogisticsPayload($request->validated());
        $payload['approval_deadline'] = now()->addHours(config('visits.approval_sla_hours', 48));

        $visit = Visit::create($payload);

        $timelineEventService->createEvent(
            'visit_created',
            'Visit scheduled for '.$visit->elder->name.' by '.$visit->user->name.' on '.$visit->visit_date->format('Y-m-d H:i'),
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
                    'label' => $visit->user->name.' - '.$visit->elder->name,
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
                    'label' => $visit->user->name.' - '.$visit->elder->name,
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
        $payload = $this->hydrateLogisticsPayload($request->validated());

        if (($payload['status'] ?? null) === 'approved') {
            $payload['approved_at'] = now();
            $payload['approved_by'] = $payload['approved_by'] ?? Auth::id();
        }

        if (($payload['status'] ?? null) === 'pending') {
            $payload['approved_at'] = null;
            $payload['approved_by'] = null;
            $payload['approval_deadline'] = $visit->approval_deadline ?? now()->addHours(
                config('visits.approval_sla_hours', 48)
            );
        }

        $visit->update($payload);

        $timelineEventService->createEvent(
            'visit_updated',
            'Visit for '.$visit->elder->name.' by '.$visit->user->name.' updated to status: '.$visit->status,
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

    /**
     * Normalize optional logistics and SLA payload values.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function hydrateLogisticsPayload(array $payload): array
    {
        $payload['needs_translator'] = (bool) Arr::get($payload, 'needs_translator', false);
        if (! $payload['needs_translator']) {
            $payload['translator_language'] = null;
        }

        $payload['needs_transport'] = (bool) Arr::get($payload, 'needs_transport', false);
        if (! $payload['needs_transport']) {
            $payload['transport_preference'] = null;
        }

        if (array_key_exists('logistics_notes', $payload)) {
            $payload['logistics_notes'] = trim((string) $payload['logistics_notes']) ?: null;
        }

        return $payload;
    }

    /**
     * Build summary stats for the dashboard cards.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function visitStats(): array
    {
        $pending = Visit::where('status', 'pending')->count();
        $thisWeek = Visit::whereBetween('visit_date', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();
        $translator = Visit::where('needs_translator', true)->count();

        return [
            [
                'label' => 'Pending approvals',
                'value' => $pending,
            ],
            [
                'label' => 'Scheduled this week',
                'value' => $thisWeek,
                'tone' => 'success',
            ],
            [
                'label' => 'Translator requests',
                'value' => $translator,
                'tone' => 'muted',
            ],
        ];
    }

    /**
     * Build a calendar payload for the SPA grid.
     */
    protected function calendarPayload(Request $request): array
    {
        $monthString = (string) $request->input('month', now()->format('Y-m'));

        try {
            $month = Carbon::createFromFormat('Y-m', $monthString)->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->startOfMonth();
        }

        $startOfCalendar = $month->copy()->startOfMonth()->startOfWeek();
        $endOfCalendar = $month->copy()->endOfMonth()->endOfWeek();

        $visitsByDay = Visit::with(['elder:id,first_name,last_name', 'user:id,name'])
            ->whereBetween('visit_date', [$startOfCalendar, $endOfCalendar])
            ->orderBy('visit_date')
            ->get()
            ->groupBy(fn (Visit $visit) => $visit->visit_date->toDateString());

        $days = [];
        $today = now()->toDateString();
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        foreach (CarbonPeriod::create($startOfCalendar, $endOfCalendar) as $date) {
            $dateKey = $date->toDateString();

            $days[] = [
                'date' => $dateKey,
                'label' => $date->format('j'),
                'isCurrentMonth' => $date->betweenIncluded($monthStart, $monthEnd),
                'isToday' => $dateKey === $today,
                'visits' => ($visitsByDay[$dateKey] ?? collect())->map(function (Visit $visit) {
                    return [
                        'id' => $visit->id,
                        'time' => $visit->visit_date->format('H:i'),
                        'elder_name' => $visit->elder->name,
                        'visitor_name' => $visit->user->name,
                        'status' => $visit->status,
                    ];
                })->values(),
            ];
        }

        $query = array_filter($request->except(['month', 'page']), fn ($value) => $value !== null && $value !== '');

        return [
            'monthLabel' => $month->format('F Y'),
            'currentMonth' => $month->format('Y-m'),
            'previousUrl' => route('visits.index', array_merge($query, [
                'month' => $month->copy()->subMonth()->format('Y-m'),
            ])),
            'nextUrl' => route('visits.index', array_merge($query, [
                'month' => $month->copy()->addMonth()->format('Y-m'),
            ])),
            'days' => $days,
        ];
    }

    /**
     * Visits approaching or exceeding the SLA deadline.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function slaAlerts(): array
    {
        $threshold = now()->addHours(config('visits.reminder_threshold_hours', 12));

        return Visit::with(['elder:id,first_name,last_name', 'user:id,name', 'branch:id,name'])
            ->where('status', 'pending')
            ->whereNotNull('approval_deadline')
            ->where('approval_deadline', '<=', $threshold)
            ->orderBy('approval_deadline')
            ->limit(12)
            ->get()
            ->map(function (Visit $visit) {
                $deadline = $visit->approval_deadline;

                return [
                    'id' => $visit->id,
                    'elder_name' => $visit->elder->name,
                    'visitor_name' => $visit->user->name,
                    'branch_name' => $visit->branch->name,
                    'deadline' => optional($deadline)?->toIso8601String(),
                    'state' => optional($deadline)?->isPast() ? 'breached' : 'warning',
                    'deadline_for_humans' => optional($deadline)?->diffForHumans(null, true),
                ];
            })
            ->values()
            ->all();
    }
}
