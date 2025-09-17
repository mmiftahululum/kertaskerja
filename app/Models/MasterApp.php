<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterApp extends Model
{
    use HasFactory;

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
}
