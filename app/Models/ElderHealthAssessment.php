<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElderHealthAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'elder_id',
        'assessment_date',
        'summary',
        'mobility_level',
        'risk_level',
        'created_by',
    ];

    protected $casts = [
        'assessment_date' => 'date',
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
