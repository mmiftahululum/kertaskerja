<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('child_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('head_status_id')->constrained('head_statuses')->onDelete('cascade');
            $table->string('status_name');
            $table->string('status_code');
            $table->string('status_color', 7); // untuk hex color #FFFFFF
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('child_statuses');
    }
};
