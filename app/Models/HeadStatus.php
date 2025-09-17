<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_status_name'
    ];

    public function childStatuses()
    {
        return $this->hasMany(ChildStatus::class);
    }
}