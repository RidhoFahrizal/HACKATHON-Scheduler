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
        $schedules = Schedule::all();
        $kaprodi = User::where('email', 'kaprodi@univ.ac.id')->first();
        $mahasiswa = User::where('email', 'admin@univ.ac.id')->first();
        $rooms = Room::where('is_active', true)->get();

        if ($schedules->isEmpty() || !$kaprodi || !$mahasiswa || $rooms->isEmpty()) {
            return;
        }

        $requests = [
            [
                'request_code' => 'RSQ-2026-001',
                'schedule_id' => $schedules[0]->id,
                'requester_id' => $mahasiswa->id,
                'target_date' => now()->addDays(3)->toDateString(),
                'target_day' => 'Kamis',
                'target_start_time' => '08:00',
                'target_end_time' => '10:30',
                'target_room_id' => $rooms[1]->id,
                'duration_type' => '1_minggu',
                'reason' => 'Dosen berhalangan hadir karena tugas dinas ke luar kota. Mohon jadwal digeser ke hari Kamis.',
                'urgency' => 'Tinggi',
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'is_active' => true,
            ],
            [
                'request_code' => 'RSQ-2026-002',
                'schedule_id' => $schedules[1]->id,
                'requester_id' => $mahasiswa->id,
                'target_date' => now()->addDays(5)->toDateString(),
                'target_day' => 'Jumat',
                'target_start_time' => '13:00',
                'target_end_time' => '15:30',
                'target_room_id' => $rooms[2]->id,
                'duration_type' => '1_minggu',
                'reason' => 'Ruangan sedang digunakan untuk ujian semester. Perlu pindah ruangan sementara.',
                'urgency' => 'Normal',
                'status' => 'approved',
                'reviewed_by' => $kaprodi->id,
                'reviewed_at' => now()->subDays(1),
                'review_notes' => 'Disetujui. Pastikan ruangan pengganti sudah dikonfirmasi.',
                'is_active' => true,
            ],
            [
                'request_code' => 'RSQ-2026-003',
                'schedule_id' => $schedules[2]->id,
                'requester_id' => $mahasiswa->id,
                'target_date' => now()->addDays(7)->toDateString(),
                'target_day' => 'Senin',
                'target_start_time' => '10:00',
                'target_end_time' => '12:30',
                'target_room_id' => $rooms[0]->id,
                'duration_type' => '2_minggu',
                'reason' => 'Dosen pembimbing sedang mengikuti konferensi internasional selama 2 minggu.',
                'urgency' => 'Rendah',
                'status' => 'rejected',
                'reviewed_by' => $kaprodi->id,
                'reviewed_at' => now()->subDays(2),
                'review_notes' => 'Mohon cari dosen pengganti sementara. Reschedule 2 minggu terlalu lama.',
                'is_active' => true,
            ],
            [
                'request_code' => 'RSQ-2026-004',
                'schedule_id' => $schedules[3]->id,
                'requester_id' => $mahasiswa->id,
                'target_date' => now()->addDays(2)->toDateString(),
                'target_day' => 'Rabu',
                'target_start_time' => '09:00',
                'target_end_time' => '11:30',
                'target_room_id' => $rooms[3]->id,
                'duration_type' => '1_minggu',
                'reason' => 'AC di ruangan rusak dan sedang dalam perbaikan. Kelas perlu dipindahkan.',
                'urgency' => 'Tinggi',
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'is_active' => true,
            ],
            [
                'request_code' => 'RSQ-2026-005',
                'schedule_id' => $schedules->count() > 4 ? $schedules[4]->id : $schedules[0]->id,
                'requester_id' => $mahasiswa->id,
                'target_date' => now()->addDays(10)->toDateString(),
                'target_day' => 'Selasa',
                'target_start_time' => '14:00',
                'target_end_time' => '16:30',
                'target_room_id' => $rooms->first()->id,
                'duration_type' => '1_minggu',
                'reason' => 'Ada kegiatan kampus (wisuda) yang menggunakan ruangan pada jadwal regular.',
                'urgency' => 'Normal',
                'status' => 'approved',
                'reviewed_by' => $kaprodi->id,
                'reviewed_at' => now()->subHours(6),
                'review_notes' => 'OK, sudah dicek ketersediaannya.',
                'is_active' => true,
            ],
        ];

        foreach ($requests as $request) {
            RescheduleRequest::updateOrCreate(
                ['request_code' => $request['request_code']],
                $request
            );
        }
    }
}
