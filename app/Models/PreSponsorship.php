<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\Sponsorship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PreSponsorship extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'relationship_type',
        'donation_id',
        'elder_id',
        'branch_id',
        'amount',
        'currency',
        'status',
        'notes',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function sponsorship(): HasOne
    {
        return $this->hasOne(Sponsorship::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
