<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class SchedulingConfigSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTimeSlots();
        $this->seedSettings();
    }

    private function seedTimeSlots(): void
    {
        $slotDuration = 50;
        $startHour = 8;
        $totalSlots = 16;

        for ($i = 0; $i < $totalSlots; $i++) {
            $startMinutes = $startHour * 60 + ($i * $slotDuration);
            $endMinutes = $startMinutes + $slotDuration;

            $startHourSlot = intdiv($startMinutes, 60);
            $startMin = $startMinutes % 60;
            $endHourSlot = intdiv($endMinutes, 60);
            $endMin = $endMinutes % 60;

            $startTime = sprintf('%02d:%02d', $startHourSlot, $startMin);
            $endTime = sprintf('%02d:%02d', $endHourSlot, $endMin);

            $isBlocked = false;
            $blockReason = null;

            if ($startTime >= '12:00' && $startTime < '13:00') {
                $isBlocked = true;
                $blockReason = 'lunch_break';
            }

            TimeSlot::updateOrCreate(
                ['slot_index' => $i],
                [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_blocked' => $isBlocked,
                    'block_reason' => $blockReason,
                ]
            );
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'slot_duration_minutes', 'value' => '50', 'type' => 'integer', 'description' => 'Durasi per slot dalam menit'],
            ['key' => 'work_start_hour', 'value' => '8', 'type' => 'integer', 'description' => 'Jam mulai kerja'],
            ['key' => 'work_end_hour', 'value' => '16', 'type' => 'integer', 'description' => 'Jam akhir kerja'],
            ['key' => 'lunch_break_start', 'value' => '12:00', 'type' => 'string', 'description' => 'Jam mulai istirahat'],
            ['key' => 'lunch_break_end', 'value' => '13:00', 'type' => 'string', 'description' => 'Jam akhir istirahat'],
            ['key' => 'friday_prayer_start', 'value' => '11:20', 'type' => 'string', 'description' => 'Jam mulai sholat Jumat'],
            ['key' => 'max_options', 'value' => '10', 'type' => 'integer', 'description' => 'Jumlah maksimal opsi yang ditampilkan'],
            ['key' => 'penalty_student_conflict', 'value' => '50', 'type' => 'integer', 'description' => 'Penalti maksimal untuk mahasiswa bentrok'],
            ['key' => 'penalty_lunch_proximity', 'value' => '15', 'type' => 'integer', 'description' => 'Penalti maksimal untuk dekat jam istirahat'],
            ['key' => 'penalty_lecturer_proximity', 'value' => '20', 'type' => 'integer', 'description' => 'Penalti maksimal untuk dekat matkul dosen'],
            ['key' => 'penalty_early_morning', 'value' => '15', 'type' => 'integer', 'description' => 'Penalti maksimal untuk jam terlalu pagi'],
            ['key' => 'penalty_late_afternoon', 'value' => '15', 'type' => 'integer', 'description' => 'Penalti maksimal untuk jam terlalu sore'],
            ['key' => 'penalty_capacity', 'value' => '50', 'type' => 'integer', 'description' => 'Penalti maksimal untuk kesesuaian kapasitas'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
