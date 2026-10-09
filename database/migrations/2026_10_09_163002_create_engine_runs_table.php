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
        Schema::create('engine_runs', function (Blueprint $table) {
            $table->id();
            $table->uuid('schedule_id');
            $table->string('scope');
            $table->date('target_date');
            $table->integer('target_week');
            $table->boolean('success');
            $table->json('options')->nullable();
            $table->text('thinking_log')->nullable();
            $table->timestamps();
            
            $table->foreign('schedule_id')->references('id')->on('schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engine_runs');
    }
};
