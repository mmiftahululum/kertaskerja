<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_status_id',
        'status_name',
        'status_code',
        'status_color'
    ];

    public function headStatus()
    {
        return $this->belongsTo(HeadStatus::class);
    }
}