<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElderDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'elder_id',
        'type',
        'label',
        'file_path',
        'file_name',
        'mime_type',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function elder(): BelongsTo
    {
        return $this->belongsTo(Elder::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
