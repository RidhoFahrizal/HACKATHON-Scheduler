<?php

namespace Database\Seeders;

use App\Models\SystemAuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@univ.ac.id')->first();
        $kaprodi = User::where('email', 'kaprodi@univ.ac.id')->first();

        $entries = [
            [
                'timestamp' => now()->subDays(5)->subHours(2),
                'level' => 'info',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Auth',
                'action' => 'Login',
                'details' => 'User login berhasil dari IP 192.168.1.10',
                'ip_address' => '192.168.1.10',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(5)->subHours(1),
                'level' => 'info',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Scheduling',
                'action' => 'Generate Schedule',
                'details' => 'Menjalankan engine scheduling untuk Semester Ganjil 2026 dengan 30 mata kuliah',
                'ip_address' => '192.168.1.10',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(4)->subHours(3),
                'level' => 'info',
                'actor_id' => $kaprodi?->id,
                'actor_name' => $kaprodi?->name ?? 'Kaprodi',
                'module' => 'Auth',
                'action' => 'Login',
                'details' => 'User login berhasil dari IP 192.168.1.25',
                'ip_address' => '192.168.1.25',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(4)->subHours(2),
                'level' => 'info',
                'actor_id' => $kaprodi?->id,
                'actor_name' => $kaprodi?->name ?? 'Kaprodi',
                'module' => 'Reschedule',
                'action' => 'Approve Request',
                'details' => 'Menyetujui permintaan reschedule RSQ-2026-002 untuk mata kuliah Struktur Data',
                'ip_address' => '192.168.1.25',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(4)->subHours(1),
                'level' => 'warning',
                'actor_id' => $kaprodi?->id,
                'actor_name' => $kaprodi?->name ?? 'Kaprodi',
                'module' => 'Reschedule',
                'action' => 'Reject Request',
                'details' => 'Menolak permintaan reschedule RSQ-2026-003. Alasan: durasi terlalu lama, perlu dosen pengganti.',
                'ip_address' => '192.168.1.25',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(3)->subHours(5),
                'level' => 'info',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Room',
                'action' => 'Create Booking',
                'details' => 'Booking ruangan GA-101 untuk hari Rabu slot 6-8 berhasil dibuat',
                'ip_address' => '192.168.1.10',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(3)->subHours(3),
                'level' => 'error',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Scheduling',
                'action' => 'Engine Run Failed',
                'details' => 'Engine scheduling gagal untuk scope "weekly" pada minggu 8: konflik ruangan tidak dapat diselesaikan',
                'ip_address' => '192.168.1.10',
                'status' => 'Gagal',
            ],
            [
                'timestamp' => now()->subDays(2)->subHours(4),
                'level' => 'info',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Scheduling',
                'action' => 'Engine Run Success',
                'details' => 'Engine scheduling berhasil untuk scope "weekly" pada minggu 9 dengan skor 87/100',
                'ip_address' => '192.168.1.10',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(2)->subHours(2),
                'level' => 'info',
                'actor_id' => $kaprodi?->id,
                'actor_name' => $kaprodi?->name ?? 'Kaprodi',
                'module' => 'Setting',
                'action' => 'Update Setting',
                'details' => 'Mengubah penalty_student_conflict dari 50 menjadi 60',
                'ip_address' => '192.168.1.25',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(1)->subHours(6),
                'level' => 'info',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Room',
                'action' => 'Update Room',
                'details' => 'Memperbarui kapasitas ruangan GB-201 dari 25 menjadi 30',
                'ip_address' => '192.168.1.10',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(1)->subHours(4),
                'level' => 'warning',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Auth',
                'action' => 'Failed Login',
                'details' => 'Percobaan login gagal untuk email unknown@univ.ac.id dari IP 10.0.0.55',
                'ip_address' => '10.0.0.55',
                'status' => 'Gagal',
            ],
            [
                'timestamp' => now()->subDays(1)->subHours(2),
                'level' => 'info',
                'actor_id' => $kaprodi?->id,
                'actor_name' => $kaprodi?->name ?? 'Kaprodi',
                'module' => 'Reschedule',
                'action' => 'Approve Request',
                'details' => 'Menyetujui permintaan reschedule RSQ-2026-005 untuk kegiatan wisuda',
                'ip_address' => '192.168.1.25',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subHours(8),
                'level' => 'info',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Scheduling',
                'action' => 'Generate Schedule',
                'details' => 'Menjalankan engine scheduling untuk scope "full_semester" dengan 30 mata kuliah dan 28 ruangan',
                'ip_address' => '192.168.1.10',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subHours(3),
                'level' => 'info',
                'actor_id' => $kaprodi?->id,
                'actor_name' => $kaprodi?->name ?? 'Kaprodi',
                'module' => 'Auth',
                'action' => 'Login',
                'details' => 'User login berhasil dari IP 192.168.1.25',
                'ip_address' => '192.168.1.25',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subHours(1),
                'level' => 'info',
                'actor_id' => $admin?->id,
                'actor_name' => $admin?->name ?? 'Admin Sistem',
                'module' => 'Chat',
                'action' => 'AI Response',
                'details' => 'AI assistant memberikan 3 opsi slot untuk reschedule mata kuliah Kecerdasan Buatan',
                'ip_address' => '192.168.1.10',
                'status' => 'Sukses',
            ],
        ];

        foreach ($entries as $entry) {
            SystemAuditLog::create($entry);
        }
    }
}
