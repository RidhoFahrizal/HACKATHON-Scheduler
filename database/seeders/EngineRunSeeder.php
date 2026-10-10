<?php

namespace Database\Seeders;

use App\Models\EngineRun;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class EngineRunSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = Schedule::all();

        if ($schedules->isEmpty()) return;

        $runs = [
            [
                'schedule_id' => $schedules[0]->id,
                'scope' => 'weekly',
                'target_date' => now()->subDays(7)->toDateString(),
                'target_week' => 8,
                'success' => false,
                'options' => [
                    ['day' => 3, 'startSlot' => 6, 'endSlot' => 8, 'room' => 'GA-101', 'score' => 45],
                    ['day' => 4, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GA-201', 'score' => 38],
                ],
                'thinking_log' => "Analisis konflik:\n- Bentrok ruangan GA-101 dengan mata kuliah Basis Data\n- Bentrok dosen dengan jadwal Pemrograman Web\n- Skor terendah: 45 (di bawah threshold 50)\n- Tidak ditemukan solusi layak untuk minggu 8",
            ],
            [
                'schedule_id' => $schedules[0]->id,
                'scope' => 'weekly',
                'target_date' => now()->subDays(3)->toDateString(),
                'target_week' => 9,
                'success' => true,
                'options' => [
                    ['day' => 1, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GA-101', 'score' => 92],
                    ['day' => 2, 'startSlot' => 3, 'endSlot' => 5, 'room' => 'GA-201', 'score' => 85],
                    ['day' => 3, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GB-101', 'score' => 78],
                ],
                'thinking_log' => "Analisis minggu 9:\n- Tidak ada konflik ruangan\n- Dosen tersedia di semua slot yang diusulkan\n- Kapasitas ruangan sesuai (40/40)\n- Skor tertinggi: 92 (sangat baik)\n- Opsi 1 direkomendasikan",
            ],
            [
                'schedule_id' => $schedules->count() > 1 ? $schedules[1]->id : $schedules[0]->id,
                'scope' => 'full_semester',
                'target_date' => now()->subDays(2)->toDateString(),
                'target_week' => 10,
                'success' => true,
                'options' => [
                    ['day' => 1, 'startSlot' => 3, 'endSlot' => 5, 'room' => 'GB-101', 'score' => 88],
                    ['day' => 2, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GA-102', 'score' => 82],
                    ['day' => 3, 'startSlot' => 6, 'endSlot' => 8, 'room' => 'GB-201', 'score' => 76],
                    ['day' => 4, 'startSlot' => 3, 'endSlot' => 5, 'room' => 'GA-202', 'score' => 71],
                    ['day' => 5, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GC-101', 'score' => 68],
                ],
                'thinking_log' => "Full semester scheduling:\n- 30 mata kuliah, 12 dosen, 28 ruangan\n- Iterasi 1: 5 konflik terdeteksi\n- Iterasi 2: 2 konflik tersisa\n- Iterasi 3: Semua konflik terselesaikan\n- Total skor rata-rata: 77/100\n- Waktu komputasi: 3.2 detik",
            ],
            [
                'schedule_id' => $schedules->count() > 2 ? $schedules[2]->id : $schedules[0]->id,
                'scope' => 'reschedule',
                'target_date' => now()->subDays(1)->toDateString(),
                'target_week' => 10,
                'success' => true,
                'options' => [
                    ['day' => 4, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GA-201', 'score' => 88],
                    ['day' => 4, 'startSlot' => 3, 'endSlot' => 5, 'room' => 'GA-102', 'score' => 75],
                    ['day' => 5, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GA-301', 'score' => 70],
                ],
                'thinking_log' => "Reschedule request RSQ-2026-001:\n- Mencari slot pengganti untuk Kecerdasan Buatan\n- Kendala: dosen tidak tersedia hari Selasa\n- Slot Kamis pagi: skor 88 (optimal, tidak ada bentrok)\n- Slot Kamis siang: skor 75 (sedikit dekat jam istirahat)\n- Slot Jumat pagi: skor 70 (ruangan jauh dari gedung dosen)",
            ],
            [
                'schedule_id' => $schedules->count() > 3 ? $schedules[3]->id : $schedules[0]->id,
                'scope' => 'weekly',
                'target_date' => now()->toDateString(),
                'target_week' => 10,
                'success' => true,
                'options' => [
                    ['day' => 2, 'startSlot' => 0, 'endSlot' => 2, 'room' => 'GB-202', 'score' => 95],
                    ['day' => 3, 'startSlot' => 3, 'endSlot' => 5, 'room' => 'GA-101', 'score' => 89],
                    ['day' => 4, 'startSlot' => 6, 'endSlot' => 8, 'room' => 'GB-102', 'score' => 83],
                ],
                'thinking_log' => "Weekly scheduling minggu 10:\n- Semua ruangan tersedia\n- Tidak ada konflik dosen\n- Kapasitas sesuai untuk semua kelas\n- Skor tertinggi: 95 (excellent)\n- Distribusi jadwal merata sepanjang minggu",
            ],
        ];

        foreach ($runs as $run) {
            EngineRun::create($run);
        }
    }
}
