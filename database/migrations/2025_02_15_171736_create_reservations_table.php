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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->char('reservation_type', 1)->nullable();  
            $table->string('name')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->char('password', 4)->nullable();
            $table->string('church')->nullable();
            $table->string('church_phone')->nullable();
            $table->string('pastor_name')->nullable();
            $table->string('church_address')->nullable();
            $table->string('organization')->nullable();
            $table->string('leader')->nullable();
            $table->string('event_name')->nullable();
            $table->char('office_phone', 11)->nullable();
            $table->string('address')->nullable();  
            $table->char('room_worship_type', 1)->nullable();    
            $table->json('room_reservation')->nullable();
            $table->json('worship_reservation')->nullable();
            $table->json('cafeteria_reservation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
