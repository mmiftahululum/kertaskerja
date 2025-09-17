<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->bigIncrements('id'); // primary auto increment
            $table->string('nama_karyawan');
            $table->string('username_git')->unique();
            $table->string('username_vpn')->nullable();
            $table->date('tanggal_berakhir_kontrak')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('karyawans');
    }
};
