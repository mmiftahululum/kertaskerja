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
    Schema::table('tasks', function (Blueprint $table) {
        // Tambahkan kolom untuk menyimpan urutan, setelah kolom 'title'
        $table->integer('order_column')->default(0)->after('title');
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn('order_column');
    });
}
};
