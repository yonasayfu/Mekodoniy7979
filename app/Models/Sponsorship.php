<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sponsorship extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = [
        'user_id',
        'elder_id',
        'amount',
        'currency', // Add currency here
        'frequency',
        'start_date',
        'end_date',
        'status',
        'notes',
        'subscription_id',
        'next_billing_date',
    ];

    /**
     * Get the user that owns the sponsorship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the elder that the sponsorship is for.
     */
    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}
