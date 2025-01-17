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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('board');
            $table->unsignedBigInteger('order')->default(0);
            $table->string('category')->nullable();
            $table->boolean('public')->default(true);
            $table->string('title');
            $table->text('content')->nullable();
            $table->unsignedInteger('goal_price')->default(0);
            $table->unsignedInteger('current_price')->default(0);
            $table->unsignedInteger('file_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
