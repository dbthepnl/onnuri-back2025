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
        Schema::create('form_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order')->default(0);
            $table->unsignedInteger('form_id')->default(0);
            $table->string('element')->nullable();
            $table->string('element_name')->nullable();
            $table->string('tag')->nullable();      
            $table->integer('required')->nullable();        
            $table->string('label')->nullable();       
            $table->string('values')->nullable();       
            $table->string('sub_text')->nullable();  
            $table->string('long_text')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
