<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use App\Models\Elder;
use App\Scopes\BranchScope;
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
        'branch_id',
        'amount',
        'currency', // Add currency here
        'frequency',
        'relationship_type',
        'start_date',
        'end_date',
        'status',
        'notes',
        'subscription_id',
        'subscription_gateway',
        'subscription_metadata',
        'next_billing_date',
    ];

    protected $casts = [
        'subscription_metadata' => 'array',
        'next_billing_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function (Sponsorship $sponsorship) {
            if (! $sponsorship->branch_id) {
                $sponsorship->branch_id = $sponsorship->resolveBranchId();
            }
        });

        static::updating(function (Sponsorship $sponsorship) {
            if ($sponsorship->isDirty('elder_id') || ! $sponsorship->branch_id) {
                $sponsorship->branch_id = $sponsorship->resolveBranchId();
            }
        });
    }

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

    protected function resolveBranchId(): ?int
    {
        if ($this->branch_id) {
            return $this->branch_id;
        }

        if ($this->relationLoaded('elder') && $this->elder) {
            return $this->elder->branch_id;
        }

        if ($this->elder_id) {
            return Elder::withoutGlobalScopes()
                ->whereKey($this->elder_id)
                ->value('branch_id');
        }

        return null;
    }
}
