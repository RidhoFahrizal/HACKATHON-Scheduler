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
        Schema::create('schedule', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('roomId');
            $table->uuid('lecturerId');
            $table->string('semesterType');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('roomId')->references('id')->on('room');
            $table->foreign('lecturerId')->references('id')->on('lecturer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule');
    }
};