<?php

namespace Database\Seeders;

use App\Models\SystemAuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $baak = User::where('email', 'baak@pens.ac.id')->first()
            ?? User::where('email', 'admin@univ.ac.id')->first();
        $dosen = User::where('email', 'dosen.alpha@pens.ac.id')->first();
        $mhs = User::where('email', 'mhs.alpha@student.pens.ac.id')->first();

        $entries = [
            [
                'timestamp' => now()->subMinutes(18),
                'level' => 'shift',
                'actor_id' => $dosen?->id,
                'actor_name' => $dosen?->name ?? 'Dosen Alpha, S.Kom., M.T.',
                'module' => 'Scheduler Engine',
                'action' => 'Persetujuan Perpindahan',
                'details' => 'Jadwal Workshop Mesin Pembelajaran dipindahkan ke Rabu, 13:00 - 16:00 (Lab C 103) secara otomatis.',
                'ip_address' => '10.12.0.24',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subMinutes(30),
                'level' => 'info',
                'actor_id' => $mhs?->id,
                'actor_name' => $mhs?->name ?? 'Mahasiswa Alpha (3122000001)',
                'module' => 'Pengajuan Mahasiswa',
                'action' => 'Pengajuan Jadwal Baru',
                'details' => 'Pengajuan baru permohonan perpindahan jadwal untuk Workshop Mesin Pembelajaran diajukan.',
                'ip_address' => '10.12.5.88',
                'status' => 'Terkirim',
            ],
            [
                'timestamp' => now()->subHours(2),
                'level' => 'sync',
                'actor_id' => $baak?->id,
                'actor_name' => 'Petugas BAAK Kampus',
                'module' => 'Data Ingestion',
                'action' => 'Sinkronisasi CSV',
                'details' => 'Sinkronisasi berkas jadwal_semester_ganjil_2026.csv berhasil memvalidasi 48 baris perkuliahan.',
                'ip_address' => '10.12.0.14',
                'status' => 'Selesai',
            ],
            [
                'timestamp' => now()->subHours(4),
                'level' => 'info',
                'actor_id' => null,
                'actor_name' => 'Sistem Otomatis',
                'module' => 'Integritas Jadwal',
                'action' => 'Pemeriksaan Integritas',
                'details' => 'Pemeriksaan integritas matriks mingguan 7 hari selesai: 0 konflik jadwal terdeteksi pada 42 ruangan.',
                'ip_address' => '127.0.0.1',
                'status' => 'Optimal',
            ],
            [
                'timestamp' => now()->subHours(6),
                'level' => 'warning',
                'actor_id' => null,
                'actor_name' => 'Petugas Sarpras',
                'module' => 'Manajemen Fasilitas',
                'action' => 'Pemberitahuan Fasilitas',
                'details' => 'Pemeliharaan berkala AC di Ruang Workshop Komputer C-102 dijadwalkan akhir pekan.',
                'ip_address' => '10.12.2.11',
                'status' => 'Perhatian',
            ],
            [
                'timestamp' => now()->subHours(8),
                'level' => 'shift',
                'actor_id' => $dosen?->id,
                'actor_name' => $dosen?->name ?? 'Dosen Alpha, S.Kom., M.T.',
                'module' => 'Persetujuan Dosen',
                'action' => 'Approval Permohonan',
                'details' => 'Permohonan perpindahan jadwal Kecerdasan Komputasional disetujui Dosen.',
                'ip_address' => '10.12.0.24',
                'status' => 'Disetujui',
            ],
            [
                'timestamp' => now()->subHours(10),
                'level' => 'error',
                'actor_id' => null,
                'actor_name' => 'Parser CSV',
                'module' => 'Validasi Kurikulum',
                'action' => 'Penolakan Baris',
                'details' => 'Ditemukan kode matakuliah tidak valid (XYZ999) pada baris ke-3 berkas CSV impor. Baris ditolak.',
                'ip_address' => '10.12.0.14',
                'status' => 'Dicegah',
            ],
            [
                'timestamp' => now()->subHours(12),
                'level' => 'info',
                'actor_id' => $baak?->id,
                'actor_name' => 'Petugas BAAK Kampus',
                'module' => 'Autentikasi',
                'action' => 'Sesi Login',
                'details' => 'Sesi login petugas BAAK berhasil dari alamat jaringan internal (10.12.0.14).',
                'ip_address' => '10.12.0.14',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(1)->subHours(2),
                'level' => 'sync',
                'actor_id' => null,
                'actor_name' => 'Database Service',
                'module' => 'Pencadangan',
                'action' => 'Snapshot PostgreSQL',
                'details' => 'Snapshot harian PostgreSQL berhasil dibuat dan diarsipkan (Ukuran: 42.8 MB).',
                'ip_address' => '127.0.0.1',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(1)->subHours(5),
                'level' => 'warning',
                'actor_id' => null,
                'actor_name' => 'Scheduler Engine',
                'module' => 'Deteksi Kapasitas',
                'action' => 'Peringatan Kapasitas',
                'details' => 'Ruang Kuliah Teori SAW-06.10 terisi 38 dari 40 kapasitas maksimum pada sesi perkuliahan siang.',
                'ip_address' => '127.0.0.1',
                'status' => 'Mendekati Batas',
            ],
            [
                'timestamp' => now()->subDays(2),
                'level' => 'info',
                'actor_id' => $dosen?->id,
                'actor_name' => $dosen?->name ?? 'Dosen Alpha, S.Kom., M.T.',
                'module' => 'Asisten AI',
                'action' => 'Konsultasi Jadwal',
                'details' => 'Dosen berkonsultasi mengenai slot kosong untuk kelas Pembelajaran Mendalam.',
                'ip_address' => '10.12.0.24',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(2)->subHours(3),
                'level' => 'shift',
                'actor_id' => $baak?->id,
                'actor_name' => 'Petugas BAAK Kampus',
                'module' => 'Manajemen Jadwal',
                'action' => 'Perubahan Jadwal',
                'details' => 'Jadwal Metodologi Penelitian Rekayasa dialihkan sementara ke SAW-05.02.',
                'ip_address' => '10.12.0.14',
                'status' => 'Disetujui',
            ],
            [
                'timestamp' => now()->subDays(3),
                'level' => 'info',
                'actor_id' => $baak?->id,
                'actor_name' => 'Petugas BAAK Kampus',
                'module' => 'Master Data',
                'action' => 'Perbarui Ruangan',
                'details' => 'Menambahkan data ruangan baru Laboratorium Software Terpadu SAW-08 kapasitas 50 kursi.',
                'ip_address' => '10.12.0.14',
                'status' => 'Sukses',
            ],
            [
                'timestamp' => now()->subDays(3)->subHours(4),
                'level' => 'warning',
                'actor_id' => null,
                'actor_name' => 'Scheduler Validator',
                'module' => 'Batas SKS',
                'action' => 'Validasi KRS',
                'details' => '2 mahasiswa mengajukan KRS melebihi batas 24 SKS, memerlukan persetujuan Dosen Wali.',
                'ip_address' => '127.0.0.1',
                'status' => 'Perhatian',
            ],
            [
                'timestamp' => now()->subDays(4),
                'level' => 'error',
                'actor_id' => null,
                'actor_name' => 'Network Gateway',
                'module' => 'Koneksi Gateway',
                'action' => 'Timeout Sinkronisasi',
                'details' => 'Percobaan sinkronisasi ke SIAKAD pusat mengalami timeout setelah 30 detik. Sistem menggunakan cache lokal.',
                'ip_address' => '10.12.0.1',
                'status' => 'Gagal',
            ],
        ];

        foreach ($entries as $entry) {
            SystemAuditLog::create($entry);
        }
    }
}
