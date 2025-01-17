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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('board');
            $table->unsignedBigInteger('order')->default(0);
            $table->string('category')->nullable();
            $table->boolean('public')->default(true);
            $table->string('title');
            $table->string('background_color')->nullable();
            $table->string('border_color')->nullable();
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->text('content')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
