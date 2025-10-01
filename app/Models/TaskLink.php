<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; // <-- 1. Import Trait
use Spatie\Activitylog\LogOptions; 

class TaskLink extends Model
{
    use HasFactory;
      use LogsActivity;

    protected $fillable = [
        'task_id',
        'name',
        'url'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
        'task_id',
        'name',
        'url'
    ]) // Catat hanya perubahan pada kolom ini
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Karyawan ini telah {$eventName}") // Deskripsi log
            ->useLogName('Link Task'); // Nama log untuk mempermudah filter
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}