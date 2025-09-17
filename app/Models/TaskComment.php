<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    use HasFactory;

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
