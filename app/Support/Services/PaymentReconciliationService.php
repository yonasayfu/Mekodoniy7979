<?php

namespace App\Support\Services;

use App\Models\Donation;
use App\Models\PaymentReconciliationImport;
use App\Models\PaymentReconciliationItem;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentReconciliationService
{
    public function __construct()
    {
    }

    public function import(User $user, UploadedFile $file, string $gateway, ?int $branchId = null): PaymentReconciliationImport
    {
        $branchId = $branchId ?: $user->branch_id;

        /** @var PaymentReconciliationImport $import */
        $import = PaymentReconciliationImport::create([
            'branch_id' => $branchId,
            'uploaded_by' => $user->id,
            'gateway' => $gateway,
            'source_filename' => $file->getClientOriginalName(),
            'status' => 'processing',
        ]);

        $rows = $this->streamCsv($file);

        DB::transaction(function () use ($rows, $import, $branchId, $gateway) {
            foreach ($rows as $row) {
                $item = $import->items()->create([
                    'branch_id' => $branchId,
                    'gateway' => $gateway,
                    'reference' => $row['reference'],
                    'payer_name' => $row['payer_name'],
                    'payer_phone' => $row['payer_phone'],
                    'amount' => $row['amount'],
                    'currency' => $row['currency'],
                    'paid_at' => $row['paid_at'],
                    'status' => PaymentReconciliationItem::STATUS_UNMATCHED,
                    'raw_payload' => $row['raw'],
                ]);

                $this->attemptAutoMatch($item, $gateway, $branchId);
            }
        });

        $import->recalculateStats();

        return $import->fresh(['items']);
    }

    public function manualMatch(PaymentReconciliationItem $item, string $identifier): void
    {
        $donation = $this->resolveDonationByIdentifier($identifier, $item->branch_id);

        if (! $donation) {
            throw new \InvalidArgumentException('Donation not found for the provided reference.');
        }

        $this->applyMatch($item, $donation, null, 'manual');
    }

    public function ignore(PaymentReconciliationItem $item, ?string $note = null): void
    {
        $item->forceFill([
            'status' => PaymentReconciliationItem::STATUS_IGNORED,
            'notes' => $note,
        ])->save();

        $item->import->recalculateStats();
    }

    protected function attemptAutoMatch(
        PaymentReconciliationItem $item,
        string $gateway,
        ?int $branchId = null
    ): void {
        $matchStrategy = null;
        $donation = null;
        $transaction = PaymentTransaction::withoutGlobalScopes()
            ->where('gateway', $gateway)
            ->where(function ($query) use ($item) {
                if ($item->reference) {
                    $query->where('gateway_reference', $item->reference)
                        ->orWhere('gateway_transaction_id', $item->reference);
                }
            })
            ->first();

        if ($transaction && $this->branchMatches($branchId, $transaction->branch_id)) {
            $donation = $transaction->donation;
            $matchStrategy = 'transaction_reference';
        }

        if (! $donation && $item->reference) {
            $potential = Donation::withoutGlobalScopes()
                ->where(function ($query) use ($item) {
                    $query->where('payment_id', $item->reference)
                        ->orWhere('receipt_uuid', $item->reference);
                })
                ->first();

            if ($potential && $this->branchMatches($branchId, $potential->branch_id)) {
                $donation = $potential;
                $matchStrategy = 'donation_reference';
            }
        }

        if (! $donation && $item->amount && $item->paid_at) {
            $candidate = Donation::withoutGlobalScopes()
                ->whereDate('created_at', $item->paid_at->toDateString())
                ->where('amount', $item->amount)
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->first();

            if ($candidate) {
                $donation = $candidate;
                $matchStrategy = 'amount_date';
            }
        }

        if ($donation) {
            $this->applyMatch($item, $donation, $transaction, $matchStrategy ?? 'auto', false);
        }
    }

    protected function applyMatch(
        PaymentReconciliationItem $item,
        Donation $donation,
        ?PaymentTransaction $transaction,
        string $strategy,
        bool $recalculate = true
    ): void {
        $item->forceFill([
            'status' => PaymentReconciliationItem::STATUS_MATCHED,
            'branch_id' => $donation->branch_id,
            'donation_id' => $donation->id,
            'elder_id' => $donation->elder_id,
            'payment_transaction_id' => $transaction?->id,
            'match_strategy' => $strategy,
        ])->save();

        if ($recalculate) {
            $item->import->recalculateStats();
        }
    }

    protected function branchMatches(?int $scopeBranchId, ?int $recordBranchId): bool
    {
        if (! $scopeBranchId) {
            return true;
        }

        return (int) $scopeBranchId === (int) $recordBranchId;
    }

    protected function resolveDonationByIdentifier(string $identifier, ?int $branchId = null): ?Donation
    {
        $query = Donation::withoutGlobalScopes()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        if (is_numeric($identifier)) {
            return $query->whereKey((int) $identifier)->first();
        }

        return $query
            ->where(function ($inner) use ($identifier) {
                $inner->where('receipt_uuid', $identifier)
                    ->orWhere('payment_id', $identifier);
            })
            ->first();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function streamCsv(UploadedFile $file): array
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return $rows;
        }

        $header = null;
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($header === null) {
                $header = $this->normalizeHeader($data);
                continue;
            }

            $row = $this->mapRow($header, $data);
            if (! array_filter($row)) {
                continue;
            }

            $rows[] = [
                'reference' => Arr::get($row, 'reference'),
                'payer_name' => Arr::get($row, 'payer_name'),
                'payer_phone' => Arr::get($row, 'payer_phone'),
                'amount' => $this->toDecimal(Arr::get($row, 'amount')),
                'currency' => Arr::get($row, 'currency', 'ETB'),
                'paid_at' => $this->toCarbon(Arr::get($row, 'paid_at')),
                'raw' => $row,
            ];
        }

        fclose($handle);

        return $rows;
    }

    protected function normalizeHeader(array $header): array
    {
        return array_map(function ($value) {
            $normalized = Str::of((string) $value)->lower()->snake()->value();

            return match ($normalized) {
                'transaction_reference', 'txn_reference', 'reference_number' => 'reference',
                'msisdn', 'phone', 'mobile_number' => 'payer_phone',
                'customer_name', 'payer' => 'payer_name',
                'amount_etb', 'etb_amount' => 'amount',
                'timestamp', 'transacted_at' => 'paid_at',
                default => $normalized,
            };
        }, $header);
    }

    protected function mapRow(array $header, array $row): array
    {
        $mapped = [];
        foreach ($header as $index => $column) {
            $mapped[$column] = $row[$index] ?? null;
        }

        return $mapped;
    }

    protected function toDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) str_replace([',', ' '], '', (string) $value);
    }

    protected function toCarbon($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
