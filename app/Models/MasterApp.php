<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; // <-- 1. Import Trait
use Spatie\Activitylog\LogOptions; 

class MasterApp extends Model
{
    use HasFactory;
 use LogsActivity;

    protected $table = 'master_apps';

    protected $fillable = [
        'nama_apps',
        'gitaws',
        'domain_url_prod',
        'domain_url_dev',
        'username_login_dev',
        'password_login_dev',
        'db_IP_port_dev',
        'db_name',
        'db_username',
        'db_password',
    ];

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
        'nama_apps',
        'gitaws',
        'domain_url_prod',
        'domain_url_dev',
        'username_login_dev',
        'password_login_dev',
        'db_IP_port_dev',
        'db_name',
        'db_username',
        'db_password',
    ]) // Catat hanya perubahan pada kolom ini
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Karyawan ini telah {$eventName}") // Deskripsi log
            ->useLogName('Master APP'); // Nama log untuk mempermudah filter
    }
}
