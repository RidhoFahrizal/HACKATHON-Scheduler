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
            $table->integer('day');
            $table->integer('startSlot');
            $table->integer('endSlot');
            $table->uuid('subjectId');
            $table->uuid('lecturerId');
            $table->uuid('roomId');
            $table->string('semesterType');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('subjectId')->references('id')->on('subject');
            $table->foreign('lecturerId')->references('id')->on('lecturer');
            $table->foreign('roomId')->references('id')->on('room');
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