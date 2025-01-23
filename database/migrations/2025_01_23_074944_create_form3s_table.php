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
        Schema::create('form3s', function (Blueprint $table) {
            $table->id();
            $table->boolean('public')->default(true);
            $table->foreignId('user_id')->nullable();
            $table->integer('board_id')->default(0);
            $table->integer('cardinal_id')->default(0);
            $table->json('church_info')->nullable();
            $table->json('work_info')->nullable();
            $table->json('disciple_info')->nullable();
            $table->json('faith_info')->nullable();
            $table->json('mission_info')->nullable();
            $table->json('dispatch_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form3s');
    }
};
