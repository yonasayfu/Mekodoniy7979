<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualReport extends Model
{
    protected $fillable = [
        'user_id',
        'report_year',
        'impact_data',
        'pdf_path',
    ];

    protected $casts = [
        'impact_data' => 'array',
        'report_year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
