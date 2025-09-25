<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatusLog extends Model
{
    use HasFactory;

    protected $table = 'task_status_logs';

     protected $fillable = [
        'task_id',
        'user_id',
        'child_status_id',
        'keterangan',
    ];

    public $timestamps = true;   // created_at / updated_at

    // Relasi
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(ChildStatus::class, 'child_status_id');
    }
}
