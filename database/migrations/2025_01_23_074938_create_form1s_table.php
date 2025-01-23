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
        Schema::create('form1s', function (Blueprint $table) {
            $table->id();
            $table->boolean('public')->default(true);
            $table->foreignId('user_id')->nullable();
            $table->integer('board_id')->default(0);
            $table->integer('category_id')->default(0);
            $table->string('name_ko')->nullable();
            $table->string('name_ch')->nullable();
            $table->string('name_en')->nullable();
            $table->string('passport_number')->nullable();
            $table->char('birth', 8)->nullable();  
            $table->integer('birth_type')->default(0);
            $table->integer('gender')->default(0);
            $table->integer('blood_type')->default(0);
            $table->integer('marriage')->default(0);
            $table->string('residence_number')->nullable();
            $table->char('phone', 11)->nullable();  
            $table->string('address')->nullable();
            $table->string('detail_address')->nullable();
            $table->string('zip_code')->nullable();  
            $table->string('my_profile_photo')->nullable();  
            $table->string('emergency_name')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->char('emergency_phone', 11)->nullable();  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form1s');
    }
};
