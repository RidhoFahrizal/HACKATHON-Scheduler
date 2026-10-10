<?php

namespace Database\Seeders;

use App\Models\RescheduleRequest;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class RescheduleRequestSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = Schedule::with(['subject', 'lecturer', 'room'])->get();
        if ($schedules->isEmpty()) {
            return;
        }

        $mhsUser = User::where('email', 'mhs.alpha@student.pens.ac.id')->first()
            ?? User::whereHas('role', fn ($q) => $q->where('code', 'mahasiswa'))->first();
        $dosenUser = User::where('email', 'dosen.alpha@pens.ac.id')->first()
            ?? User::whereHas('role', fn ($q) => $q->where('code', 'dosen'))->first();
        $baakUser = User::where('email', 'baak@pens.ac.id')->first()
            ?? User::whereHas('role', fn ($q) => $q->where('code', 'baak'))->first();

        $rooms = Room::where('is_active', true)->get()->keyBy('code');
        $fallbackRoom = Room::where('is_active', true)->first();

        $reqList = [
            [
                'request_code' => 'REQ-0001',
                'schedule_index' => 0,
                'requester' => $mhsUser,
                'target_date' => now()->addDays(3)->toDateString(),
                'target_day' => 'Rabu',
                'target_start_time' => '13:00',
                'target_end_time' => '16:00',
                'target_room' => $rooms['C-103'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Tabrakan jadwal ujian sertifikasi internasional kompetensi cloud',
                'urgency' => 'Tinggi',
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ],
            [
                'request_code' => 'REQ-0002',
                'schedule_index' => 9, // KCK
                'requester' => $dosenUser,
                'target_date' => now()->addDays(2)->toDateString(),
                'target_day' => 'Jumat',
                'target_start_time' => '13:00',
                'target_end_time' => '16:00',
                'target_room' => $rooms['C-105'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Penugasan dewan riset vokasi nasional di Jakarta',
                'urgency' => 'Normal',
                'status' => 'approved',
                'reviewed_by' => $dosenUser,
                'reviewed_at' => now()->subDays(1),
                'review_notes' => 'Disetujui. Ruangan C-105 tersedia.',
            ],
            [
                'request_code' => 'REQ-0003',
                'schedule_index' => 1, // PMJ
                'requester' => $dosenUser,
                'target_date' => now()->addDays(4)->toDateString(),
                'target_day' => 'Selasa',
                'target_start_time' => '08:00',
                'target_end_time' => '11:00',
                'target_room' => $rooms['C-105'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Pemeliharaan berkala server workstation lab jaringan',
                'urgency' => 'Normal',
                'status' => 'approved',
                'reviewed_by' => $baakUser,
                'reviewed_at' => now()->subDays(2),
                'review_notes' => 'Disetujui. Server maintenance terjadwal.',
            ],
            [
                'request_code' => 'REQ-0004',
                'schedule_index' => 16, // PCD
                'requester' => $mhsUser,
                'target_date' => now()->addDays(5)->toDateString(),
                'target_day' => 'Jumat',
                'target_start_time' => '08:00',
                'target_end_time' => '10:00',
                'target_room' => $rooms['D4-201'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Kunjungan supervisi industri mahasiswa magang',
                'urgency' => 'Normal',
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ],
            [
                'request_code' => 'REQ-0005',
                'schedule_index' => 5, // MET
                'requester' => $dosenUser,
                'target_date' => now()->addDays(6)->toDateString(),
                'target_day' => 'Kamis',
                'target_start_time' => '15:00',
                'target_end_time' => '17:00',
                'target_room' => $rooms['SAW-05.02'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Rapat koordinasi senat akademik politeknik',
                'urgency' => 'Tinggi',
                'status' => 'approved',
                'reviewed_by' => $baakUser,
                'reviewed_at' => now()->subDays(1),
                'review_notes' => 'Disetujui. Jadwal senat diprioritaskan.',
            ],
            [
                'request_code' => 'REQ-0006',
                'schedule_index' => 6, // KW
                'requester' => $mhsUser,
                'target_date' => now()->addDays(7)->toDateString(),
                'target_day' => 'Rabu',
                'target_start_time' => '09:00',
                'target_end_time' => '11:00',
                'target_room' => $rooms['SAW-06.10'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Jadwal kuliah tamu inkubator bisnis',
                'urgency' => 'Rendah',
                'status' => 'rejected',
                'reviewed_by' => $dosenUser,
                'reviewed_at' => now()->subDays(2),
                'review_notes' => 'Ruangan SAW-06.10 terisi penuh pada slot tersebut.',
            ],
            [
                'request_code' => 'REQ-0007',
                'schedule_index' => 14, // PA1
                'requester' => $dosenUser,
                'target_date' => now()->addDays(8)->toDateString(),
                'target_day' => 'Senin',
                'target_start_time' => '09:00',
                'target_end_time' => '12:00',
                'target_room' => $rooms['SAW-08'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Penyamaan jadwal evaluasi berkala tahap 1 proyek akhir',
                'urgency' => 'Normal',
                'status' => 'approved',
                'reviewed_by' => $baakUser,
                'reviewed_at' => now()->subDays(3),
                'review_notes' => 'Disetujui.',
            ],
            [
                'request_code' => 'REQ-0008',
                'schedule_index' => 20, // BIK
                'requester' => $mhsUser,
                'target_date' => now()->addDays(4)->toDateString(),
                'target_day' => 'Kamis',
                'target_start_time' => '10:00',
                'target_end_time' => '12:00',
                'target_room' => $rooms['B-101'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Persiapan pameran pekan ilmiah mahasiswa politeknik',
                'urgency' => 'Normal',
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ],
            [
                'request_code' => 'REQ-0009',
                'schedule_index' => 19, // PM
                'requester' => $dosenUser,
                'target_date' => now()->addDays(5)->toDateString(),
                'target_day' => 'Sabtu',
                'target_start_time' => '08:00',
                'target_end_time' => '11:00',
                'target_room' => $rooms['Lab Riset Lt 3'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Akomodasi jadwal kelas praktisi industri terapan',
                'urgency' => 'Normal',
                'status' => 'approved',
                'reviewed_by' => $baakUser,
                'reviewed_at' => now()->subDays(2),
                'review_notes' => 'Disetujui.',
            ],
            [
                'request_code' => 'REQ-0010',
                'schedule_index' => 10, // K3L
                'requester' => $dosenUser,
                'target_date' => now()->addDays(3)->toDateString(),
                'target_day' => 'Senin',
                'target_start_time' => '15:00',
                'target_end_time' => '17:00',
                'target_room' => $rooms['SAW-06.10'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Simulasi tanggap darurat evakuasi gedung dan kebakaran',
                'urgency' => 'Mendesak',
                'status' => 'approved',
                'reviewed_by' => $baakUser,
                'reviewed_at' => now()->subHours(12),
                'review_notes' => 'Disetujui untuk simulasi K3L.',
            ],
            [
                'request_code' => 'REQ-0011',
                'schedule_index' => 15, // PMS
                'requester' => $mhsUser,
                'target_date' => now()->addDays(2)->toDateString(),
                'target_day' => 'Selasa',
                'target_start_time' => '15:00',
                'target_end_time' => '17:00',
                'target_room' => $rooms['SAW-05.02'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Kegiatan perlombaan robotika nasional (KRI)',
                'urgency' => 'Tinggi',
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ],
            [
                'request_code' => 'REQ-0012',
                'schedule_index' => 18, // KP
                'requester' => $dosenUser,
                'target_date' => now()->addDays(5)->toDateString(),
                'target_day' => 'Rabu',
                'target_start_time' => '10:00',
                'target_end_time' => '12:00',
                'target_room' => $rooms['B-204'] ?? $fallbackRoom,
                'duration_type' => '1_minggu',
                'reason' => 'Supervisi langsung ke mitra industri politeknik di Surabaya',
                'urgency' => 'Normal',
                'status' => 'approved',
                'reviewed_by' => $baakUser,
                'reviewed_at' => now()->subDays(4),
                'review_notes' => 'Disetujui.',
            ],
        ];

        foreach ($reqList as $item) {
            $schedule = $schedules[$item['schedule_index'] % $schedules->count()];

            RescheduleRequest::updateOrCreate(
                ['request_code' => $item['request_code']],
                [
                    'request_code' => $item['request_code'],
                    'schedule_id' => $schedule->id,
                    'requester_id' => $item['requester']->id,
                    'target_date' => $item['target_date'],
                    'target_day' => $item['target_day'],
                    'target_start_time' => $item['target_start_time'],
                    'target_end_time' => $item['target_end_time'],
                    'target_room_id' => $item['target_room']->id,
                    'duration_type' => $item['duration_type'],
                    'reason' => $item['reason'],
                    'urgency' => $item['urgency'],
                    'status' => $item['status'],
                    'reviewed_by' => $item['reviewed_by']?->id,
                    'reviewed_at' => $item['reviewed_at'],
                    'review_notes' => $item['review_notes'],
                    'is_active' => true,
                ]
            );
        }
    }
}
