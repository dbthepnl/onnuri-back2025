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
        Schema::create('form8s', function (Blueprint $table) {
            $table->id();
            $table->boolean('public')->default(true);
            $table->foreignId('user_id')->nullable();
            $table->integer('board_id')->default(0);
            $table->integer('cardinal_id')->default(0);
            $table->json('vision_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form8s');
    }
};
