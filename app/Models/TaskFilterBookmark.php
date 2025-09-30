<?php

// app/Models/TaskFilterBookmark.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskFilterBookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'filters',
    ];

    protected $casts = [
        'filters' => 'array', // Otomatis konversi JSON ke array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}