<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('timestamp')->useCurrent();
            $table->string('level', 20)->default('info');
            // actor_name is a snapshot so entries stay readable after the user row is deleted.
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name', 150);
            $table->string('module', 100);
            $table->string('action', 150);
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 30)->default('Sukses');

            $table->index(['level', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_audit_logs');
    }
};
