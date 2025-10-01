// app/Models/TaskAssignment.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity; // <-- 1. Import Trait
use Spatie\Activitylog\LogOptions; 

class TaskAssignment extends Model
{
    use HasFactory;
     use LogsActivity;


    protected $fillable = [
        'task_id',
        'employee_id',
        'assigned_at',
        'completed_at',
        'is_completed',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['task_id', 'employee_id', 'assigned_at']) // Catat hanya perubahan pada kolom ini
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Karyawan ini telah {$eventName}") // Deskripsi log
            ->useLogName('Assignment'); // Nama log untuk mempermudah filter
    }

    // Relasi: Tugas
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Relasi: Karyawan
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
