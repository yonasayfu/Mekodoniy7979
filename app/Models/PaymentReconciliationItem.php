<?php

namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReconciliationItem extends Model
{
    use HasFactory;

    public const STATUS_MATCHED = 'matched';
    public const STATUS_UNMATCHED = 'unmatched';
    public const STATUS_IGNORED = 'ignored';

    protected $fillable = [
        'payment_reconciliation_import_id',
        'branch_id',
        'donation_id',
        'payment_transaction_id',
        'elder_id',
        'gateway',
        'reference',
        'payer_name',
        'payer_phone',
        'amount',
        'currency',
        'paid_at',
        'status',
        'match_strategy',
        'raw_payload',
        'notes',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(PaymentReconciliationImport::class, 'payment_reconciliation_import_id');
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }
}
