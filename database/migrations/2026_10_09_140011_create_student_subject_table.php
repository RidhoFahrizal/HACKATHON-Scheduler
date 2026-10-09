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
        Schema::create('studentSubject', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('studentId');
            $table->uuid('subjectId');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('studentId')->references('id')->on('student');
            $table->foreign('subjectId')->references('id')->on('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studentSubject');
    }
};