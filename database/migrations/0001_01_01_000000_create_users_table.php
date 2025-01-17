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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 16);
            $table->char('phone', 11)->unique();  
            $table->string('name');
            $table->char('birth', 8)->nullable();  
            $table->integer('birth_type')->default(0);
            $table->string('zip_code')->nullable();  
            $table->string('my_profile_photo')->nullable();  
            $table->string('address')->nullable();
            $table->string('detail_address')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->char('homepage', 60)->nullable();
            $table->integer('officers')->default(0);
            $table->integer('gender')->default(0);
            $table->integer('email_agree')->default(0);
            $table->integer('personal_agree')->default(0);
            $table->integer('sms_agree')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
