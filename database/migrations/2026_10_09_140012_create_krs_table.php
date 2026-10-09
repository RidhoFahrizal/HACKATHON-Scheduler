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
        Schema::create('krs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('studentSubjectId');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraint
            $table->foreign('studentSubjectId')->references('id')->on('studentSubject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};