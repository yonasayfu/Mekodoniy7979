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
        'current_status',
        'admitted_at',
        'deceased_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admitted_at' => 'datetime',
        'deceased_at' => 'datetime',
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
     * Get the sponsorships for the elder.
     */
    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
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

    public function statusEvents(): HasMany
    {
        return $this->hasMany(ElderStatusEvent::class);
    }

    public function healthAssessments(): HasMany
    {
        return $this->hasMany(ElderHealthAssessment::class);
    }

    public function medicalConditions(): HasMany
    {
        return $this->hasMany(ElderMedicalCondition::class);
    }

    public function medications(): HasMany
    {
        return $this->hasMany(ElderMedication::class);
    }

    // Accessor for full name
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
