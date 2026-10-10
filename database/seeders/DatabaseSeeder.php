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
        ]);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );
    }
}
