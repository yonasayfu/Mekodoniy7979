<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SponsorshipProposal extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'elder_id',
        'donor_id',
        'proposed_by',
        'amount',
        'frequency',
        'relationship_type',
        'notes',
        'status',
        'expires_at',
        'responded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markExpired(): void
    {
        $this->forceFill([
            'status' => self::STATUS_EXPIRED,
            'responded_at' => $this->responded_at ?? Carbon::now(),
        ])->save();
    }
}
