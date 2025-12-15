<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'elder_id',
        'branch_id',
        'visit_date',
        'purpose',
        'notes',
        'status',
    ];

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
}
