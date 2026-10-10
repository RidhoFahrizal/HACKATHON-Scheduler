<?php

namespace Database\Seeders;

use App\Models\Lecturer;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $lecturers = Lecturer::all()->keyBy('email');

        $alpha = $lecturers['dosen.alpha@pens.ac.id'] ?? $lecturers->first();
        $beta = $lecturers['dosen.beta@pens.ac.id'] ?? $lecturers->first();
        $gamma = $lecturers['dosen.gamma@pens.ac.id'] ?? $lecturers->first();
        $delta = $lecturers['dosen.delta@pens.ac.id'] ?? $lecturers->first();
        $epsilon = $lecturers['dosen.epsilon@pens.ac.id'] ?? $lecturers->first();
        $zeta = $lecturers['dosen.zeta@pens.ac.id'] ?? $lecturers->first();
        $eta = $lecturers['dosen.eta@pens.ac.id'] ?? $lecturers->first();

        $subjects = [
            // PENS Curriculum Subjects
            [
                'code' => 'WMP301',
                'name' => 'Workshop Mesin Pembelajaran',
                'credits' => 3,
                'semester' => 5,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $alpha->id,
            ],
            [
                'code' => 'PMJ301',
                'name' => 'Pemrograman Jaringan Lanjut',
                'credits' => 3,
                'semester' => 5,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $beta->id,
            ],
            [
                'code' => 'MET201',
                'name' => 'Metodologi Penelitian Rekayasa',
                'credits' => 2,
                'semester' => 5,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $zeta->id,
            ],
            [
                'code' => 'KWR201',
                'name' => 'Kewirausahaan Teknologi',
                'credits' => 2,
                'semester' => 5,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $gamma->id,
            ],
            [
                'code' => 'K3L201',
                'name' => 'Keamanan, Keselamatan & K3L',
                'credits' => 2,
                'semester' => 1,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $epsilon->id,
            ],
            [
                'code' => 'PRO401',
                'name' => 'Proyek Akhir Tahap 1',
                'credits' => 4,
                'semester' => 7,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $delta->id,
            ],
            [
                'code' => 'PCD201',
                'name' => 'Pengolahan Citra Digital',
                'credits' => 2,
                'semester' => 5,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $epsilon->id,
            ],
            [
                'code' => 'KPR201',
                'name' => 'Kerja Praktek Industri',
                'credits' => 2,
                'semester' => 5,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $delta->id,
            ],
            [
                'code' => 'BIK201',
                'name' => 'Bahasa Inggris Komunikasi Profesi',
                'credits' => 2,
                'semester' => 3,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $eta->id,
            ],
            [
                'code' => 'KCK301',
                'name' => 'Kecerdasan Komputasional',
                'credits' => 3,
                'semester' => 7,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $alpha->id,
            ],
            [
                'code' => 'PMS201',
                'name' => 'Pemodelan & Simulasi Sistem',
                'credits' => 2,
                'semester' => 3,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $alpha->id,
            ],
            [
                'code' => 'PM201',
                'name' => 'Pembelajaran Mendalam',
                'credits' => 3,
                'semester' => 1,
                'department' => 'Pascasarjana Terapan',
                'lecturerId' => $alpha->id,
            ],
            [
                'code' => 'PBA201',
                'name' => 'Pengolahan Bahasa Alami',
                'credits' => 2,
                'semester' => 5,
                'department' => 'D4 Teknik Informatika',
                'lecturerId' => $alpha->id,
            ],

            // Core Computer Science
            [
                'code' => 'ALP101',
                'name' => 'Algoritma dan Pemrograman',
                'credits' => 3,
                'semester' => 1,
                'department' => 'Teknik Informatika',
                'lecturerId' => $lecturers['dosen.theta@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'STD102',
                'name' => 'Struktur Data',
                'credits' => 3,
                'semester' => 2,
                'department' => 'Teknik Informatika',
                'lecturerId' => $lecturers['dosen.theta@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'BSD201',
                'name' => 'Basis Data',
                'credits' => 3,
                'semester' => 3,
                'department' => 'Sistem Informasi',
                'lecturerId' => $lecturers['dosen.kappa@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'PWB202',
                'name' => 'Pemrograman Web',
                'credits' => 3,
                'semester' => 3,
                'department' => 'Teknik Komputer',
                'lecturerId' => $lecturers['dosen.lambda@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'PBM301',
                'name' => 'Pemrograman Mobile',
                'credits' => 3,
                'semester' => 4,
                'department' => 'Teknik Komputer',
                'lecturerId' => $lecturers['dosen.lambda@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'KCB302',
                'name' => 'Kecerdasan Buatan',
                'credits' => 3,
                'semester' => 4,
                'department' => 'Sains Komputasi',
                'lecturerId' => $lecturers['dosen.iota@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'MCL401',
                'name' => 'Machine Learning',
                'credits' => 4,
                'semester' => 5,
                'department' => 'Sains Komputasi',
                'lecturerId' => $lecturers['dosen.iota@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'JKO201',
                'name' => 'Jaringan Komputer',
                'credits' => 3,
                'semester' => 3,
                'department' => 'Jaringan Komputer',
                'lecturerId' => $lecturers['dosen.mu@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'SIS202',
                'name' => 'Sistem Operasi',
                'credits' => 3,
                'semester' => 3,
                'department' => 'Jaringan Komputer',
                'lecturerId' => $lecturers['dosen.mu@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'RPL301',
                'name' => 'Rekayasa Perangkat Lunak',
                'credits' => 3,
                'semester' => 4,
                'department' => 'Rekayasa Perangkat Lunak',
                'lecturerId' => $lecturers['dosen.xi@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'IMK302',
                'name' => 'Interaksi Manusia dan Komputer',
                'credits' => 3,
                'semester' => 4,
                'department' => 'Rekayasa Perangkat Lunak',
                'lecturerId' => $lecturers['dosen.xi@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'KIN401',
                'name' => 'Keamanan Informasi',
                'credits' => 3,
                'semester' => 5,
                'department' => 'Keamanan Siber',
                'lecturerId' => $lecturers['dosen.nu@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'KRG402',
                'name' => 'Kriptografi',
                'credits' => 3,
                'semester' => 6,
                'department' => 'Keamanan Siber',
                'lecturerId' => $lecturers['dosen.nu@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'MTD101',
                'name' => 'Matematika Diskrit',
                'credits' => 3,
                'semester' => 1,
                'department' => 'Matematika Komputasi',
                'lecturerId' => $lecturers['dosen.omicron@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'ALJ102',
                'name' => 'Aljabar Linear',
                'credits' => 3,
                'semester' => 2,
                'department' => 'Matematika Komputasi',
                'lecturerId' => $lecturers['dosen.omicron@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'STP201',
                'name' => 'Statistika dan Probabilitas',
                'credits' => 3,
                'semester' => 3,
                'department' => 'Statistika Terapan',
                'lecturerId' => $lecturers['dosen.pi@univ.ac.id']->id ?? $alpha->id,
            ],
            [
                'code' => 'IOT402',
                'name' => 'Internet of Things Terapan',
                'credits' => 3,
                'semester' => 6,
                'department' => 'Teknik Komputer',
                'lecturerId' => $beta->id,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
    }
}
