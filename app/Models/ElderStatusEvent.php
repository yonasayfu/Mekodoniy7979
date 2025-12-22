<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElderStatusEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'elder_id',
        'from_status',
        'to_status',
        'reason',
        'occurred_at',
        'created_by',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
