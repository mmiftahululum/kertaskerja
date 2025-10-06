<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TaskFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'file_path',
        'file_name',
        'mime_type',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

     public function getFileTypeAttribute(): string
    {
        $mime = $this->mime_type;

        if (Str::startsWith($mime, 'image/')) {
            return 'image';
        }
        if ($mime === 'application/pdf') {
            return 'pdf';
        }
        if (in_array($mime, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'word';
        }
        if (in_array($mime, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return 'excel';
        }
        if (in_array($mime, ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'])) {
            return 'archive';
        }
        if (Str::startsWith($mime, 'video/')) {
            return 'video';
        }
        if (Str::startsWith($mime, 'audio/')) {
            return 'audio';
        }

        return 'default'; // Ikon default untuk tipe file lainnya
    }

     public function getFormattedSizeAttribute()
    {
        $bytes = Storage::size($this->file_path);
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    // Relasi: Tugas yang file ini terkait
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Relasi: Pengguna yang mengupload
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }
}
