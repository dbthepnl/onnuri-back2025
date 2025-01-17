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
        Schema::create('cardinals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name_en');
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->unsignedBigInteger('cardinal_number')->nullable();
            $table->unsignedBigInteger('trainers')->default(0);
            $table->string('trainer_place');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cardinals');
    }
};
