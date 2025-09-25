<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';

    protected $fillable = [
        'nama_karyawan',
        'nickname',
        'email',
        'phone_no',
        'username_git',
        'username_vpn',
        'tanggal_berakhir_kontrak',
        'sebagai',
    ];
    protected $casts = [
        'tanggal_berakhir_kontrak' => 'date',
    ];
}
