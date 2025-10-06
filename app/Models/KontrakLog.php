<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontrakLog extends Model
{
    use HasFactory;

    protected $table = 'kontrak_logs';

    protected $fillable = [
        'karyawan_id',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status_kontrak',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}