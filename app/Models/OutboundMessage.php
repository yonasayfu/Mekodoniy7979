<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboundMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'elder_id',
        'channel',
        'to',
        'subject',
        'content',
        'template',
        'template_data',
        'status',
        'external_id',
        'error_message',
        'attempts',
        'sent_at',
        'delivered_at',
        'failed_at',
    ];

    protected $casts = [
        'template_data' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeOlderThan($query, int $hours)
    {
        return $query->where('created_at', '<', now()->subHours($hours));
    }

    // Helper methods
    public function markAsSending(): bool
    {
        return $this->update([
            'status' => 'sending',
            'attempts' => $this->attempts + 1,
        ]);
    }

    public function markAsSent(string $externalId = null): bool
    {
        return $this->update([
            'status' => 'sent',
            'external_id' => $externalId,
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markAsFailed(string $errorMessage): bool
    {
        return $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'failed_at' => now(),
        ]);
    }

    public function markAsDelivered(): bool
    {
        return $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function canRetry(): bool
    {
        return $this->status === 'failed' && $this->attempts < config('outbound.max_attempts', 3);
    }
}
