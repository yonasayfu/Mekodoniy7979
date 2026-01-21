<?php

namespace App\Models;

use App\Models\Elder;
use App\Scopes\BranchScope;
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
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function (CaseNote $caseNote) {
            if (! $caseNote->branch_id) {
                $caseNote->branch_id = $caseNote->inferBranchId();
            }
        });

        static::updating(function (CaseNote $caseNote) {
            if ($caseNote->isDirty('elder_id') || ! $caseNote->branch_id) {
                $caseNote->branch_id = $caseNote->inferBranchId();
            }
        });
    }

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

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments()
    {
        return $this->hasMany(CaseNoteAttachment::class);
    }

    public function versions()
    {
        return $this->hasMany(CaseNoteVersion::class)->latest();
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

    protected function inferBranchId(): ?int
    {
        if ($this->branch_id) {
            return $this->branch_id;
        }

        if ($this->relationLoaded('elder') && $this->elder) {
            return $this->elder->branch_id;
        }

        if ($this->elder_id) {
            return Elder::withoutGlobalScopes()
                ->whereKey($this->elder_id)
                ->value('branch_id');
        }

        return null;
    }
}
