<?php

namespace Database\Seeders;

use App\Models\AcademicCalendar;
use Illuminate\Database\Seeder;

class AcademicCalendarSeeder extends Seeder
{
    public function run(): void
    {
        AcademicCalendar::create([
            'name' => 'Semester Ganjil 2026',
            'year' => 2026,
            'semester' => 'ganjil',
            'start_date' => '2026-02-09',
            'end_date' => '2026-06-01',
            'total_weeks' => 16,
            'is_active' => true,
        ]);

        AcademicCalendar::create([
            'name' => 'Semester Genap 2026',
            'year' => 2026,
            'semester' => 'genap',
            'start_date' => '2026-07-06',
            'end_date' => '2026-10-26',
            'total_weeks' => 16,
            'is_active' => false,
        ]);
    }
}
