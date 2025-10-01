<?php

// app/Models/Timesheet.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity; // <-- 1. Import Trait
use Spatie\Activitylog\LogOptions; 

class Timesheet extends Model
{
    use HasFactory;
      use LogsActivity;

    protected $fillable = [
        'karyawan_id',
        'task_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
        'karyawan_id',
        'task_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ]) // Catat hanya perubahan pada kolom ini
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Karyawan ini telah {$eventName}") // Deskripsi log
            ->useLogName('TimeSheet'); // Nama log untuk mempermudah filter
    }


    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}