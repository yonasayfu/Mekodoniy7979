<?php

namespace App\Http\Controllers;

use App\Http\Requests\MatchReconciliationItemRequest;
use App\Http\Requests\StorePaymentReconciliationRequest;
use App\Models\Branch;
use App\Models\PaymentReconciliationImport;
use App\Models\PaymentReconciliationItem;
use App\Support\Services\PaymentReconciliationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentReconciliationController extends Controller
{
    public function __construct(private PaymentReconciliationService $service)
    {
    }

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user?->can('donations.manage'), 403);

        $imports = PaymentReconciliationImport::with('uploader:id,name', 'branch:id,name')
            ->latest()
            ->paginate(10)
            ->through(function (PaymentReconciliationImport $import) {
                return [
                    'id' => $import->id,
                    'gateway' => $import->gateway,
                    'status' => $import->status,
                    'total_rows' => $import->total_rows,
                    'matched_rows' => $import->matched_rows,
                    'unmatched_rows' => $import->unmatched_rows,
                    'ignored_rows' => $import->ignored_rows,
                    'source_filename' => $import->source_filename,
                    'branch' => $import->branch ? [
                        'id' => $import->branch->id,
                        'name' => $import->branch->name,
                    ] : null,
                    'uploaded_by' => $import->uploader ? [
                        'id' => $import->uploader->id,
                        'name' => $import->uploader->name,
                    ] : null,
                    'created_at' => $import->created_at?->toDateTimeString(),
                ];
            })
            ->withQueryString();

        $summary = PaymentReconciliationItem::selectRaw('status, COUNT(*) as count, COALESCE(SUM(amount),0) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->status => [
                'count' => (int) $row->count,
                'total' => (float) $row->total,
            ]]);

        $branchOptions = [];
        if (! $user->branch_id || $user->can('branches.manage')) {
            $branchOptions = Branch::orderBy('name')->get(['id', 'name']);
        }

        return Inertia::render('Reconciliation/Index', [
            'imports' => $imports,
            'summary' => [
                'matched' => $summary[PaymentReconciliationItem::STATUS_MATCHED] ?? ['count' => 0, 'total' => 0],
                'unmatched' => $summary[PaymentReconciliationItem::STATUS_UNMATCHED] ?? ['count' => 0, 'total' => 0],
            ],
            'gateways' => [
                ['label' => 'Telebirr', 'value' => 'telebirr'],
                ['label' => 'CBE', 'value' => 'cbe'],
            ],
            'branches' => $branchOptions,
        ]);
    }

    public function store(StorePaymentReconciliationRequest $request)
    {
        $branchId = $request->input('branch_id');
        if (! $request->user()->can('branches.manage')) {
            $branchId = $request->user()->branch_id;
        }

        $import = $this->service->import(
            $request->user(),
            $request->file('file'),
            $request->input('gateway'),
            $branchId
        );

        return redirect()->route('reconciliation.show', $import)->with('success', 'Reconciliation import processed.');
    }

    public function show(PaymentReconciliationImport $import, Request $request): Response
    {
        abort_unless($request->user()?->can('donations.manage'), 403);
        $statusFilter = $request->query('status');

        $items = $import->items()
            ->with(['donation:id,receipt_uuid,amount', 'elder:id,first_name,last_name'])
            ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $itemSummary = $import->items()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('Reconciliation/Show', [
            'import' => [
                'id' => $import->id,
                'gateway' => $import->gateway,
                'status' => $import->status,
                'total_rows' => $import->total_rows,
                'matched_rows' => $import->matched_rows,
                'unmatched_rows' => $import->unmatched_rows,
                'ignored_rows' => $import->ignored_rows,
                'source_filename' => $import->source_filename,
                'branch' => $import->branch ? [
                    'id' => $import->branch->id,
                    'name' => $import->branch->name,
                ] : null,
                'uploaded_by' => $import->uploader?->only(['id', 'name']),
                'created_at' => $import->created_at?->toDateTimeString(),
            ],
            'items' => $items->through(function (PaymentReconciliationItem $item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'reference' => $item->reference,
                    'payer_name' => $item->payer_name,
                    'payer_phone' => $item->payer_phone,
                    'amount' => $item->amount,
                    'currency' => $item->currency,
                    'paid_at' => optional($item->paid_at)->toDateTimeString(),
                    'donation' => $item->donation ? [
                        'id' => $item->donation->id,
                        'receipt_uuid' => $item->donation->receipt_uuid,
                        'amount' => $item->donation->amount,
                    ] : null,
                    'elder' => $item->elder ? [
                        'id' => $item->elder->id,
                        'name' => $item->elder->name,
                    ] : null,
                    'match_strategy' => $item->match_strategy,
                    'notes' => $item->notes,
                ];
            }),
            'filters' => [
                'status' => $statusFilter,
            ],
            'statusCounts' => [
                PaymentReconciliationItem::STATUS_MATCHED => (int) ($itemSummary[PaymentReconciliationItem::STATUS_MATCHED] ?? 0),
                PaymentReconciliationItem::STATUS_UNMATCHED => (int) ($itemSummary[PaymentReconciliationItem::STATUS_UNMATCHED] ?? 0),
                PaymentReconciliationItem::STATUS_IGNORED => (int) ($itemSummary[PaymentReconciliationItem::STATUS_IGNORED] ?? 0),
            ],
        ]);
    }

    public function matchItem(
        MatchReconciliationItemRequest $request,
        PaymentReconciliationImport $import,
        PaymentReconciliationItem $item
    ) {
        abort_unless($request->user()?->can('donations.manage'), 403);
        abort_unless($item->payment_reconciliation_import_id === $import->id, 404);

        $this->service->manualMatch($item, $request->input('donation_reference'));

        return back()->with('success', 'Entry matched successfully.');
    }

    public function ignoreItem(
        PaymentReconciliationImport $import,
        PaymentReconciliationItem $item,
        Request $request
    ) {
        abort_unless($request->user()?->can('donations.manage'), 403);
        abort_unless($item->payment_reconciliation_import_id === $import->id, 404);

        $this->service->ignore($item, $request->input('note'));

        return back()->with('success', 'Entry ignored.');
    }

}
