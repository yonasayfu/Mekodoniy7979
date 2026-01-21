<?php

namespace App\Models;

use App\Models\User;
use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CaseNoteAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_note_id',
        'branch_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    protected $appends = [
        'download_url',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new BranchScope);

        static::deleting(function (self $attachment) {
            $attachment->deleteFile();
        });
    }

    public function caseNote(): BelongsTo
    {
        return $this->belongsTo(CaseNote::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('case-notes.attachments.download', [
            'case_note' => $this->case_note_id,
            'attachment' => $this->id,
        ]);
    }

    public function storagePath(): string
    {
        return $this->file_path;
    }

    public function deleteFile(): void
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
    }
}
