<?php

namespace Database\Seeders;

use App\Models\Lecturer;
use Illuminate\Database\Seeder;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $lecturers = [
            ['username' => 'Dr. Budi Santoso, M.Kom.', 'email' => 'budi.santoso@univ.ac.id'],
            ['username' => 'Prof. Dr. Siti Rahayu, M.Sc.', 'email' => 'siti.rahayu@univ.ac.id'],
            ['username' => 'Dr. Ahmad Wijaya, M.T.', 'email' => 'ahmad.wijaya@univ.ac.id'],
            ['username' => 'Dr. Linda Kusuma, M.Kom.', 'email' => 'linda.kusuma@univ.ac.id'],
            ['username' => 'Ir. Rudi Hartono, M.Eng.', 'email' => 'rudi.hartono@univ.ac.id'],
            ['username' => 'Dr. Maya Putri, M.Si.', 'email' => 'maya.putri@univ.ac.id'],
            ['username' => 'Dr. Fajar Nugroho, M.Cs.', 'email' => 'fajar.nugroho@univ.ac.id'],
            ['username' => 'Dr. Ani Suryani, M.T.', 'email' => 'ani.suryani@univ.ac.id'],
            ['username' => 'Prof. Bambang Irawan, Ph.D.', 'email' => 'bambang.irawan@univ.ac.id'],
            ['username' => 'Dr. Eko Prasetyo, M.Kom.', 'email' => 'eko.prasetyo@univ.ac.id'],
            ['username' => 'Dr. Nur Hidayah, M.T.', 'email' => 'nur.hidayah@univ.ac.id'],
            ['username' => 'Ir. Dwi Kurniawan, M.Sc.', 'email' => 'dwi.kurniawan@univ.ac.id'],
        ];

        foreach ($lecturers as $lecturer) {
            Lecturer::updateOrCreate(
                ['email' => $lecturer['email']],
                $lecturer
            );
        }
    }
}
