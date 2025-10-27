<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserTodo extends Model
{
    use HasFactory;

    protected $guarded = []; // Atau $fillable sesuai kebutuhan

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taskComment()
    {
        return $this->belongsTo(TaskComment::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
