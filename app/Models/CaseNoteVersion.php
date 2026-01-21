<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseNoteVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_note_id',
        'edited_by',
        'content',
        'visibility',
    ];

    public function caseNote(): BelongsTo
    {
        return $this->belongsTo(CaseNote::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
