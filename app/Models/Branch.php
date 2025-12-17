<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Branch extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = [
        'name',
        'location',
        'is_active',
        'contact_person',
        'contact_email',
        'contact_phone',
        'notes',
    ];

    /**
     * Get the users for the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the elders for the branch.
     */
    public function elders(): HasMany
    {
        return $this->hasMany(Elder::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}