<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'starts_at',
        'ends_at',
        'goal_amount',
        'goal_currency',
        'status',
        'created_by',
        'short_description',
        'cta_label',
        'cta_url',
        'accent_color',
        'hero_image_path',
        'featured_video_url',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $appends = [
        'hero_image_url',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function getHeroImageUrlAttribute(): string
    {
        if (! $this->hero_image_path) {
            return asset('images/hero-father.svg');
        }

        return asset('storage/' . ltrim($this->hero_image_path, '/'));
    }
}
