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

        $subjects = [
            ['name' => 'Algoritma dan Pemrograman', 'credits' => 3, 'lecturerId' => $lecturers['budi.santoso@univ.ac.id']->id],
            ['name' => 'Struktur Data', 'credits' => 3, 'lecturerId' => $lecturers['budi.santoso@univ.ac.id']->id],
            ['name' => 'Basis Data', 'credits' => 3, 'lecturerId' => $lecturers['ahmad.wijaya@univ.ac.id']->id],
            ['name' => 'Pemrograman Web', 'credits' => 3, 'lecturerId' => $lecturers['linda.kusuma@univ.ac.id']->id],
            ['name' => 'Pemrograman Mobile', 'credits' => 3, 'lecturerId' => $lecturers['linda.kusuma@univ.ac.id']->id],
            ['name' => 'Kecerdasan Buatan', 'credits' => 3, 'lecturerId' => $lecturers['siti.rahayu@univ.ac.id']->id],
            ['name' => 'Machine Learning', 'credits' => 4, 'lecturerId' => $lecturers['siti.rahayu@univ.ac.id']->id],
            ['name' => 'Jaringan Komputer', 'credits' => 3, 'lecturerId' => $lecturers['rudi.hartono@univ.ac.id']->id],
            ['name' => 'Sistem Operasi', 'credits' => 3, 'lecturerId' => $lecturers['rudi.hartono@univ.ac.id']->id],
            ['name' => 'Rekayasa Perangkat Lunak', 'credits' => 3, 'lecturerId' => $lecturers['fajar.nugroho@univ.ac.id']->id],
            ['name' => 'Interaksi Manusia dan Komputer', 'credits' => 3, 'lecturerId' => $lecturers['fajar.nugroho@univ.ac.id']->id],
            ['name' => 'Keamanan Informasi', 'credits' => 3, 'lecturerId' => $lecturers['maya.putri@univ.ac.id']->id],
            ['name' => 'Kriptografi', 'credits' => 3, 'lecturerId' => $lecturers['maya.putri@univ.ac.id']->id],
            ['name' => 'Matematika Diskrit', 'credits' => 3, 'lecturerId' => $lecturers['ani.suryani@univ.ac.id']->id],
            ['name' => 'Aljabar Linear', 'credits' => 3, 'lecturerId' => $lecturers['ani.suryani@univ.ac.id']->id],
            ['name' => 'Statistika dan Probabilitas', 'credits' => 3, 'lecturerId' => $lecturers['bambang.irawan@univ.ac.id']->id],
            ['name' => 'Kalkulus', 'credits' => 3, 'lecturerId' => $lecturers['bambang.irawan@univ.ac.id']->id],
            ['name' => 'Pengantar Teknologi Informasi', 'credits' => 2, 'lecturerId' => $lecturers['eko.prasetyo@univ.ac.id']->id],
            ['name' => 'Etika Profesi', 'credits' => 2, 'lecturerId' => $lecturers['nur.hidayah@univ.ac.id']->id],
            ['name' => 'Bahasa Inggris Teknik', 'credits' => 2, 'lecturerId' => $lecturers['nur.hidayah@univ.ac.id']->id],
            ['name' => 'Pancasila', 'credits' => 2, 'lecturerId' => $lecturers['dwi.kurniawan@univ.ac.id']->id],
            ['name' => 'Pendidikan Agama', 'credits' => 2, 'lecturerId' => $lecturers['dwi.kurniawan@univ.ac.id']->id],
            ['name' => 'Olahraga', 'credits' => 1, 'lecturerId' => $lecturers['dwi.kurniawan@univ.ac.id']->id],
            ['name' => 'Sistem Terdistribusi', 'credits' => 3, 'lecturerId' => $lecturers['eko.prasetyo@univ.ac.id']->id],
            ['name' => 'Cloud Computing', 'credits' => 3, 'lecturerId' => $lecturers['eko.prasetyo@univ.ac.id']->id],
            ['name' => 'Deep Learning', 'credits' => 4, 'lecturerId' => $lecturers['siti.rahayu@univ.ac.id']->id],
            ['name' => 'Pengolahan Citra Digital', 'credits' => 3, 'lecturerId' => $lecturers['maya.putri@univ.ac.id']->id],
            ['name' => 'Internet of Things', 'credits' => 3, 'lecturerId' => $lecturers['rudi.hartono@univ.ac.id']->id],
            ['name' => 'Proyek Akhir', 'credits' => 6, 'lecturerId' => $lecturers['fajar.nugroho@univ.ac.id']->id],
            ['name' => 'Kerja Praktek', 'credits' => 3, 'lecturerId' => $lecturers['fajar.nugroho@univ.ac.id']->id],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['name' => $subject['name']],
                $subject
            );
        }
    }
}
