<?php

namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentReconciliationImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'uploaded_by',
        'gateway',
        'source_filename',
        'status',
        'total_rows',
        'matched_rows',
        'unmatched_rows',
        'ignored_rows',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PaymentReconciliationItem::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function recalculateStats(): void
    {
        $counts = $this->items()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $this->forceFill([
            'matched_rows' => (int) ($counts[PaymentReconciliationItem::STATUS_MATCHED] ?? 0),
            'unmatched_rows' => (int) ($counts[PaymentReconciliationItem::STATUS_UNMATCHED] ?? 0),
            'ignored_rows' => (int) ($counts[PaymentReconciliationItem::STATUS_IGNORED] ?? 0),
            'total_rows' => $this->items()->count(),
            'status' => 'completed',
            'processed_at' => now(),
        ])->save();
    }
}
