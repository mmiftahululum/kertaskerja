<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; // <-- 1. Import Trait
use Spatie\Activitylog\LogOptions; 

class Karyawan extends Model
{
    use HasFactory;
     use LogsActivity;

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

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_karyawan', 'nickname', 'email','phone_no','username_git','username_vpn','tanggal_berakhir_kontrak','sebagai',]) // Catat hanya perubahan pada kolom ini
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Karyawan ini telah {$eventName}") // Deskripsi log
            ->useLogName('Karyawan'); // Nama log untuk mempermudah filter
    }

    public function kontrakLogs()
    {
        return $this->hasMany(KontrakLog::class)->orderBy('tanggal_mulai', 'desc');
    }
}
