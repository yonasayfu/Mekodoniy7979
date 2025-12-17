<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Elder extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = [
        'branch_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'country',
        'phone',
        'bio',
        'profile_picture_path',
        'priority_level',
        'health_status',
        'special_needs',
        'monthly_expenses',
        'video_url',
        'health_conditions',
        'sponsorship_status',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);
    }

    /**
     * Get the branch that owns the elder.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the donations for the elder.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the pledges for the elder.
     */
    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    /**
     * Get the visits for the elder.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get the timeline events for the elder.
     */
    public function timelineEvents(): HasMany
    {
        return $this->hasMany(TimelineEvent::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    // Accessor for full name
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
