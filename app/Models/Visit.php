<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use App\Models\Elder;
use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Visit extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = [
        'user_id',
        'elder_id',
        'branch_id',
        'visit_date',
        'purpose',
        'notes',
        'status',
        'approved_by',
        'needs_translator',
        'translator_language',
        'needs_transport',
        'transport_preference',
        'logistics_notes',
        'approval_deadline',
        'approved_at',
        'sla_reminder_sent_at',
        'sla_breached_notified_at',
    ];

    /**
     * Casts for logistics + deadline columns.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visit_date' => 'datetime',
            'needs_translator' => 'boolean',
            'needs_transport' => 'boolean',
            'approval_deadline' => 'datetime',
            'approved_at' => 'datetime',
            'sla_reminder_sent_at' => 'datetime',
            'sla_breached_notified_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function (Visit $visit) {
            if (! $visit->branch_id) {
                $visit->branch_id = $visit->inferBranchId();
            }
        });

        static::updating(function (Visit $visit) {
            if ($visit->isDirty('elder_id') || ! $visit->branch_id) {
                $visit->branch_id = $visit->inferBranchId();
            }
        });
    }

    /**
     * Get the user who approved the visit.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user that made the visit request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the elder that was visited.
     */
    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    /**
     * Get the branch where the visit took place.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    protected function inferBranchId(): ?int
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
