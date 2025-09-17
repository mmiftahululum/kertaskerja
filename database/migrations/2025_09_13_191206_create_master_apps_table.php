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
        Schema::create('master_apps', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key auto-increment
            $table->string('nama_apps'); // Nama aplikasi (Text)
            $table->string('gitaws'); // Git AWS (Text)
            $table->string('domain_url_prod'); // Domain/URL Production (Text)
            $table->string('domain_url_dev'); // Domain/URL Development (Text)
            $table->string('username_login_dev'); // Username login development (Text)
            $table->string('password_login_dev'); // Password login development (Text)
            $table->string('db_IP_port_dev'); // IP dan port database development (Text)
            $table->string('db_name'); // Nama database (Text)
            $table->string('db_username'); // Username database (Text)
            $table->string('db_password'); // Password database (Text)
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_apps');
    }
};