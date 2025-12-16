<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'elder_id',
        'event_type', // Changed from 'type' to 'event_type'
        'description',
        'occurred_at',
    ];

    /**
     * Get the user that is associated with the timeline event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the elder that is associated with the timeline event.
     */
    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }
}
