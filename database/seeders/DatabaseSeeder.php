<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SchedulingConfigSeeder::class,
            AcademicCalendarSeeder::class,
            RoleSeeder::class,
            DemoUserSeeder::class,
            BuildingRoomSeeder::class,
            LecturerSeeder::class,
            StudentSeeder::class,
            SubjectSeeder::class,
            ScheduleSeeder::class,
            RescheduleRequestSeeder::class,
            ChatSeeder::class,
            AuditLogSeeder::class,
            EngineRunSeeder::class,
            BuildingSeeder::class,
            RoleSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
