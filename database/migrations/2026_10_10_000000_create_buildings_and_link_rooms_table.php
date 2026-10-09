<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const DEFAULT_BUILDING_ID = '00000000-0000-4000-8000-000000000001';

    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->unsignedSmallInteger('floors_count')->default(4);
            $table->timestamps();
        });

        DB::table('buildings')->insert([
            'id' => self::DEFAULT_BUILDING_ID,
            'code' => 'UNASSIGNED',
            'name' => 'Gedung belum ditentukan',
            'floors_count' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('room', function (Blueprint $table) {
            $table->uuid('building_id')->nullable();
            $table->string('code', 30)->nullable()->unique();
            $table->string('type', 50)->default('teori');
            $table->unsignedSmallInteger('floor')->default(1);
            $table->boolean('is_active')->default(true);
        });

        DB::table('room')->whereNull('building_id')->update([
            'building_id' => self::DEFAULT_BUILDING_ID,
        ]);

        DB::table('room')->whereNull('code')->get(['id'])->each(function (object $room): void {
            DB::table('room')->where('id', $room->id)->update([
                'code' => 'LEGACY-'.Str::upper(Str::substr(str_replace('-', '', $room->id), 0, 12)),
            ]);
        });

        Schema::table('room', function (Blueprint $table) {
            $table->uuid('building_id')->nullable(false)->change();
            $table->string('code', 30)->nullable(false)->change();
            $table->foreign('building_id')->references('id')->on('buildings')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('room', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropUnique(['code']);
            $table->dropColumn(['building_id', 'code', 'type', 'floor', 'is_active']);
        });

        Schema::dropIfExists('buildings');
    }
};
