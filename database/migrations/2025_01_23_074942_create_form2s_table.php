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
        Schema::create('form2s', function (Blueprint $table) {
            $table->id();
            $table->boolean('public')->default(true);
            $table->foreignId('user_id')->nullable();
            $table->integer('board_id')->default(0);
            $table->integer('cardinal_id')->default(0);
            $table->json('school_info')->nullable();
            $table->json('language_info')->nullable();
            $table->json('personal_info')->nullable();
            $table->json('technician_info')->nullable();
            $table->json('military_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form2s');
    }
};
