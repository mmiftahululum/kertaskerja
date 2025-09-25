<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('task_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')   // atau tabel user yang kamu pakai
                  ->onDelete('cascade');

            $table->foreignId('child_status_id')
                  ->constrained('child_statuses')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_status_logs');
    }
};
