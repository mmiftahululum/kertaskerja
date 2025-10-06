<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_filter_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->json('filters'); // Kolom untuk menyimpan parameter filter
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_filter_bookmarks');
    }
};
