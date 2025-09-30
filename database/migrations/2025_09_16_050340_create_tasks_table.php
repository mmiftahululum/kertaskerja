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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul tugas
            $table->text('description')->nullable(); // Deskripsi tugas
            $table->unsignedBigInteger('parent_id')->nullable(); // Untuk sub-tugas
            $table->unsignedBigInteger('head_status_id'); // Status head (dari tabel status yang sudah ada)
            $table->unsignedBigInteger('current_status_id'); // Status child (berdasarkan grups head)
            $table->timestamp('planned_start')->nullable(); // Rencana mulai
            $table->timestamp('planned_end')->nullable(); // Rencana akhir
            $table->timestamp('actual_start')->nullable(); // Fakta mulai
            $table->timestamp('actual_end')->nullable(); // Fakta akhir
            $table->integer('progress_percent')->default(0); // Progress dalam persen (0-100)

            // Relasi
            $table->foreign('head_status_id')->references('id')->on('head_statuses');
            $table->foreign('current_status_id')->references('id')->on('child_statuses');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
