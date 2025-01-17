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
         Schema::create('asks', function (Blueprint $table) {
            $table->id();
            $table->boolean('public')->default(true);
            $table->foreignId('user_id')->nullable();
            $table->string('board');
            $table->string('password')->nullable();
            $table->unsignedInteger('category')->nullable();
            $table->unsignedBigInteger('order')->default(0);
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        }); 

        /* Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreignId("comment_id")->nullable()->constrained()->onDelete("cascade");;
            $table->morphs("commentable");
            $table->text("content");
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });  */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asks');
    }
};
