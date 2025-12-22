<?php

namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'elder_id',
        'sponsorship_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'amount',
        'currency',
        'payment_gateway',
        'payment_id',
        'status',
        'notes',
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
     * Get the user that made the donation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the elder that received the donation.
     */
    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    /**
     * Get the sponsorship that the donation is for.
     */
    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }
}
