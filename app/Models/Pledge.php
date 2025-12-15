<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'elder_id',
        'amount',
        'frequency',
        'start_date',
        'end_date',
        'status',
        'notes',
        'subscription_id',
        'next_billing_date',
    ];

    /**
     * Get the user that owns the pledge.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the elder that the pledge is for.
     */
    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }
}
