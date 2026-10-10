<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject', function (Blueprint $table) {
            $table->string('code', 30)->nullable()->unique();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->string('department', 100)->nullable();
            // CSV subject imports carry no lecturer; the assignment happens when a schedule is created.
            $table->uuid('lecturerId')->nullable()->change();
        });

        Schema::table('lecturer', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->unique();
            $table->string('code', 30)->nullable()->unique();
            $table->string('academic_title', 100)->nullable();
            $table->string('department', 100)->nullable();
        });

        Schema::table('student', function (Blueprint $table) {
            $table->string('nrp', 30)->nullable()->unique();
            $table->unsignedSmallInteger('cohort_year')->nullable();
            $table->string('department', 100)->nullable();
            $table->string('email', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropUnique(['nrp']);
            $table->dropColumn(['nrp', 'cohort_year', 'department', 'email']);
        });

        Schema::table('lecturer', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropUnique(['code']);
            $table->dropColumn(['nip', 'code', 'academic_title', 'department']);
        });

        Schema::table('subject', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'semester', 'department']);
            $table->uuid('lecturerId')->nullable(false)->change();
        });
    }
};
