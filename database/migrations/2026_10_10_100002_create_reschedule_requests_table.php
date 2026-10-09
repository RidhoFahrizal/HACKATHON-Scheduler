<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reschedule_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('request_code', 20)->unique();
            $table->foreignUuid('schedule_id')->constrained('schedule')->restrictOnDelete();
            // users.id is an auto-increment bigint while the scheduling tables use UUIDs.
            $table->foreignId('requester_id')->constrained('users')->restrictOnDelete();
            $table->date('target_date');
            $table->string('target_day', 20);
            $table->time('target_start_time');
            $table->time('target_end_time');
            $table->foreignUuid('target_room_id')->constrained('room')->restrictOnDelete();
            $table->string('duration_type', 30)->default('1_minggu');
            $table->text('reason');
            $table->string('urgency', 20)->default('Normal');
            $table->string('status', 30)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reschedule_requests');
    }
};
