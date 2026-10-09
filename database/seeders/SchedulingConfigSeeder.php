<?php

namespace Database\Seeders;

use App\Domain\Scheduling\ValueObjects\TimeSlotGrid;
use App\Models\Setting;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class SchedulingConfigSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedTimeSlots();
    }

    private function seedTimeSlots(): void
    {
        $slotDuration = (int) Setting::get('slot_duration_minutes', 50);
        $slots = TimeSlotGrid::generate(
            $slotDuration,
            (int) Setting::get('min_start_hour', 7),
            (int) Setting::get('max_end_hour', 20),
            (string) Setting::get('lunch_break_start', '12:00'),
            (string) Setting::get('lunch_break_end', '13:00'),
        );

        foreach ($slots as $slot) {
            TimeSlot::updateOrCreate(
                ['slot_index' => $slot['slot_index']],
                [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_blocked' => $slot['is_blocked'],
                    'block_reason' => $slot['block_reason'],
                ]
            );
        }

        TimeSlot::where('slot_index', '>=', count($slots))->delete();
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'slot_duration_minutes', 'value' => '50', 'type' => 'integer', 'description' => 'Durasi per slot dalam menit'],
            ['key' => 'minutes_per_credit', 'value' => '50', 'type' => 'integer', 'description' => 'Durasi SKS dalam menit'],
            ['key' => 'min_start_hour', 'value' => '7', 'type' => 'integer', 'description' => 'Batas paling awal slot yang boleh dijadwalkan'],
            ['key' => 'max_end_hour', 'value' => '20', 'type' => 'integer', 'description' => 'Batas paling akhir slot yang boleh dijadwalkan'],
            ['key' => 'work_start_hour', 'value' => '8', 'type' => 'integer', 'description' => 'Jam mulai kerja'],
            ['key' => 'work_end_hour', 'value' => '16', 'type' => 'integer', 'description' => 'Jam akhir kerja'],
            ['key' => 'lunch_break_start', 'value' => '12:00', 'type' => 'string', 'description' => 'Jam mulai istirahat'],
            ['key' => 'lunch_break_end', 'value' => '13:00', 'type' => 'string', 'description' => 'Jam akhir istirahat'],
            ['key' => 'friday_prayer_start', 'value' => '11:20', 'type' => 'string', 'description' => 'Jam mulai sholat Jumat'],
            ['key' => 'max_options', 'value' => '10', 'type' => 'integer', 'description' => 'Jumlah maksimal opsi yang ditampilkan'],
            ['key' => 'minimum_success_score', 'value' => '50', 'type' => 'integer', 'description' => 'Skor minimum agar hasil dinilai layak'],
            ['key' => 'proximity_window_slots', 'value' => '2', 'type' => 'integer', 'description' => 'Jarak slot maksimum untuk penalti kedekatan'],
            ['key' => 'capacity_target_gap', 'value' => '10', 'type' => 'integer', 'description' => 'Skala selisih kapasitas untuk penalti ruang'],
            ['key' => 'early_extreme_extra_penalty', 'value' => '25', 'type' => 'integer', 'description' => 'Tambahan penalti untuk jadwal sebelum jam kerja'],
            ['key' => 'late_extreme_hourly_penalty', 'value' => '10', 'type' => 'integer', 'description' => 'Penalti tambahan per jam melewati jam kerja'],
            ['key' => 'penalty_student_conflict', 'value' => '50', 'type' => 'integer', 'description' => 'Penalti maksimal untuk mahasiswa bentrok'],
            ['key' => 'penalty_lunch_proximity', 'value' => '15', 'type' => 'integer', 'description' => 'Penalti maksimal untuk dekat jam istirahat'],
            ['key' => 'penalty_lecturer_proximity', 'value' => '20', 'type' => 'integer', 'description' => 'Penalti maksimal untuk dekat matkul dosen'],
            ['key' => 'penalty_early_morning', 'value' => '15', 'type' => 'integer', 'description' => 'Penalti maksimal untuk jam terlalu pagi'],
            ['key' => 'penalty_late_afternoon', 'value' => '15', 'type' => 'integer', 'description' => 'Penalti maksimal untuk jam terlalu sore'],
            ['key' => 'penalty_capacity', 'value' => '50', 'type' => 'integer', 'description' => 'Penalti maksimal untuk kesesuaian kapasitas'],
            ['key' => 'penalty_slot_jumping', 'value' => '10', 'type' => 'integer', 'description' => 'Penalti per slot istirahat yang dilewati'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
