<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'elder_id',
        'branch_id',
        'created_by',
        'content',
        'visibility',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function elder()
    {
        return $this->belongsTo(Elder::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeVisibleToDonors($query)
    {
        return $query->where('visibility', 'donor_visible');
    }

    public function scopeInternalOnly($query)
    {
        return $query->where('visibility', 'internal');
    }

    public function scopeForElder($query, $elderId)
    {
        return $query->where('elder_id', $elderId);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Helpers
    public function isVisibleToDonors(): bool
    {
        return $this->visibility === 'donor_visible';
    }

    public function isInternalOnly(): bool
    {
        return $this->visibility === 'internal';
    }
}
