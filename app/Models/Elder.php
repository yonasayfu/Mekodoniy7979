<?php

namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class Elder extends Model
{
    use HasFactory;

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
}
