<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\CaseNote;
use Illuminate\Support\Str;

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
        'relationship_type',
        'health_status',
        'special_needs',
        'monthly_expenses',
        'video_url',
        'consent_form_path',
        'consent_received_at',
        'consent_notes',
        'health_conditions',
        'sponsorship_status',
        'current_status',
        'admitted_at',
        'deceased_at',
        'funding_goal',
        'funding_received',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admitted_at' => 'datetime',
        'deceased_at' => 'datetime',
        'consent_received_at' => 'datetime',
        'funding_goal' => 'integer',
        'funding_received' => 'integer',
    ];

    protected $appends = [
        'profile_photo_url',
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

    public function documents(): HasMany
    {
        return $this->hasMany(ElderDocument::class);
    }

    public function sponsorshipProposals(): HasMany
    {
        return $this->hasMany(SponsorshipProposal::class);
    }

    /**
     * Get the case notes for the elder.
     */
    public function caseNotes(): HasMany
    {
        return $this->hasMany(CaseNote::class)->with('author')->latest();
    }

    // Accessor for full name
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        $path = trim((string) $this->profile_picture_path);

        if ($path === '' || Str::contains($path, 'placeholder.com')) {
            return asset('images/monk-mekodoniya.jpg');
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (Str::startsWith($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (Str::startsWith($path, 'public/')) {
            return asset(Str::after($path, 'public/'));
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
