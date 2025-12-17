<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'date',
        'total_pledged',
        'total_collected',
        'gap',
        'active_elders',
        'matched_elders',
        'active_donors',
    ];

    protected $casts = [
        'date' => 'date',
        'total_pledged' => 'decimal:2',
        'total_collected' => 'decimal:2',
        'gap' => 'decimal:2',
    ];

    public function branch()
    {
        return $table->belongsTo(Branch::class);
    }
}