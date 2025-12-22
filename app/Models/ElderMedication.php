<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElderMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'elder_id',
        'medication_name',
        'dosage',
        'frequency',
        'started_at',
        'ended_at',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }
}
