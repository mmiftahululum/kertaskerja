<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'parent_id',
        'head_status_id',
        'current_status_id',
        'planned_start',
        'planned_end',
        'actual_start',
        'actual_end',
        'progress_percent',
    ];

    protected $casts = [
        'planned_start' => 'datetime',
        'planned_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'progress_percent' => 'integer',
    ];

    

    // Relasi: Parent task (untuk sub-tugas)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // Relasi: Child tasks (tugas anak)
    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    // Relasi: Status Head (dari tabel head_statuses)
    public function headStatus(): BelongsTo
    {
        return $this->belongsTo(HeadStatus::class, 'head_status_id');
    }

    // Relasi: Status Child (dari tabel status_child)
    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(ChildStatus::class, 'current_status_id');
    }

    // Relasi: File yang dilampirkan
    public function files(): HasMany
    {
        return $this->hasMany(TaskFile::class);
    }

    // Relasi: Komentar dan progress
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    // Relasi: Karyawan yang diassign
    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(Karyawan::class, 'task_assignments', 'task_id', 'karyawan_id')
            ->withPivot(['assigned_at', 'completed_at', 'is_completed'])
            ->withTimestamps();
    }

    // Helper: Hitung progress berdasarkan tugas assign
    public function calculateProgress(): int
    {
        $assigned = $this->assignments->count();
        $completed = $this->assignments->where('is_completed', true)->count();

        if ($assigned === 0) return 0;

        return (int) floor(($completed / $assigned) * 100);
    }

    // Helper: Cek apakah tugas sudah selesai semua
    public function isCompleted(): bool
    {
        return $this->assignments->where('is_completed', false)->count() === 0;
    }

    // Scope: Filter tugas yang sedang berjalan
    public function scopeActive($query)
    {
        return $query->whereNull('actual_end');
    }

    // Scope: Hanya tugas yang terhapus
    public function scopeTrashed($query)
    {
        return $query->onlyTrashed();
    }

    // Scope: Dengan tugas yang terhapus
    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    // Di app/Models/Task.php
    public function links(): HasMany
    {
        return $this->hasMany(TaskLink::class);
    }

    public function getLinksCountAttribute()
    {
        return $this->links()->count();
    }
    
}
