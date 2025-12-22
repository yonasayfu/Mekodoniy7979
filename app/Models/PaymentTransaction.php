<?php

namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'branch_id',
        'gateway',
        'gateway_reference',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'raw_payload',
        'processed_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'processed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
