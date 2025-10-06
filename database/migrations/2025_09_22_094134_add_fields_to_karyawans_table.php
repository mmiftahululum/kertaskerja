<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('karyawans', function (Blueprint $table) {
    $table->string('nickname')->nullable()->after('nama_karyawan');
    $table->string('email')->nullable()->unique()->after('nickname');
    $table->string('phone_no')->nullable()->after('email');
});

    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'email', 'phone_no']);
        });
    }
};
