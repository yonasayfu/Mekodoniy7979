<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElderMedicalCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'elder_id',
        'condition_name',
        'diagnosed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'diagnosed_at' => 'date',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }
}
