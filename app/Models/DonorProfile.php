<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'relationship_goal',
        'monthly_budget',
        'frequency',
        'preferred_contact_method',
        'contact_channels',
        'payment_preference',
        'notes',
        'onboarding_step',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'contact_channels' => 'array',
        'monthly_budget' => 'decimal:2',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
