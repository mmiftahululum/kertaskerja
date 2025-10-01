<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity; // <-- 1. Import Trait
use Spatie\Activitylog\LogOptions; 


class TaskComment extends Model
{
    use HasFactory;
     use LogsActivity;


    
    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
        'metadata',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['task_id', 'user_id', 'comment']) // Catat hanya perubahan pada kolom ini
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Karyawan ini telah {$eventName}") // Deskripsi log
            ->useLogName('Comment'); // Nama log untuk mempermudah filter
    }


    // Relasi: Tugas yang dikomentari
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Relasi: Pengguna yang berkomentar
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
