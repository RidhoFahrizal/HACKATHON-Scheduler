<?php

namespace Database\Seeders;

use App\Models\Lecturer;
use Illuminate\Database\Seeder;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $lecturers = [
            // Fictional academic instructors (no real individuals)
            [
                'username' => 'Dosen Alpha, S.Kom., M.T.',
                'email' => 'dosen.alpha@pens.ac.id',
                'nip' => '198001012005011001',
                'code' => 'D-ALP',
                'academic_title' => 'S.Kom., M.T.',
                'department' => 'Departemen Teknik Informatika',
            ],
            [
                'username' => 'Dosen Beta, S.Kom., M.T.',
                'email' => 'dosen.beta@pens.ac.id',
                'nip' => '198202022006021002',
                'code' => 'D-BET',
                'academic_title' => 'S.Kom., M.T.',
                'department' => 'Departemen Teknik Informatika',
            ],
            [
                'username' => 'Dosen Gamma, S.Kom., M.T.',
                'email' => 'dosen.gamma@pens.ac.id',
                'nip' => '197903032005011003',
                'code' => 'D-GAM',
                'academic_title' => 'S.Kom., M.T.',
                'department' => 'Departemen Teknik Informatika',
            ],
            [
                'username' => 'Dosen Delta, S.ST., M.Tr.Kom.',
                'email' => 'dosen.delta@pens.ac.id',
                'nip' => '198804042015042004',
                'code' => 'D-DEL',
                'academic_title' => 'S.ST., M.Tr.Kom.',
                'department' => 'Departemen Teknik Informatika',
            ],
            [
                'username' => 'Dosen Epsilon, S.Kom., M.Kom.',
                'email' => 'dosen.epsilon@pens.ac.id',
                'nip' => '198505052010121005',
                'code' => 'D-EPS',
                'academic_title' => 'S.Kom., M.Kom.',
                'department' => 'Departemen Teknik Informatika',
            ],
            [
                'username' => 'Dosen Zeta, Ph.D.',
                'email' => 'dosen.zeta@pens.ac.id',
                'nip' => '197006061995121006',
                'code' => 'D-ZET',
                'academic_title' => 'Ph.D.',
                'department' => 'Departemen Teknik Informatika',
            ],
            [
                'username' => 'Dosen Eta, Ph.D.',
                'email' => 'dosen.eta@pens.ac.id',
                'nip' => '197207071999031007',
                'code' => 'D-ETA',
                'academic_title' => 'Ph.D.',
                'department' => 'Departemen Teknik Informatika',
            ],
            [
                'username' => 'Dosen Theta, M.Kom.',
                'email' => 'dosen.theta@univ.ac.id',
                'nip' => '197508082000031008',
                'code' => 'D-THE',
                'academic_title' => 'Dr., M.Kom.',
                'department' => 'Teknik Informatika',
            ],
            [
                'username' => 'Dosen Iota, M.Sc.',
                'email' => 'dosen.iota@univ.ac.id',
                'nip' => '196809091993032009',
                'code' => 'D-IOT',
                'academic_title' => 'Prof. Dr., M.Sc.',
                'department' => 'Sains Komputasi',
            ],
            [
                'username' => 'Dosen Kappa, M.T.',
                'email' => 'dosen.kappa@univ.ac.id',
                'nip' => '197310101998021010',
                'code' => 'D-KAP',
                'academic_title' => 'Dr., M.T.',
                'department' => 'Sistem Informasi',
            ],
            [
                'username' => 'Dosen Lambda, M.Kom.',
                'email' => 'dosen.lambda@univ.ac.id',
                'nip' => '198011112005012011',
                'code' => 'D-LAM',
                'academic_title' => 'Dr., M.Kom.',
                'department' => 'Teknik Komputer',
            ],
            [
                'username' => 'Dosen Mu, M.Eng.',
                'email' => 'dosen.mu@univ.ac.id',
                'nip' => '197112121997021012',
                'code' => 'D-MU',
                'academic_title' => 'Ir., M.Eng.',
                'department' => 'Jaringan Komputer',
            ],
            [
                'username' => 'Dosen Nu, M.Si.',
                'email' => 'dosen.nu@univ.ac.id',
                'nip' => '198201012008122013',
                'code' => 'D-NU',
                'academic_title' => 'Dr., M.Si.',
                'department' => 'Keamanan Siber',
            ],
            [
                'username' => 'Dosen Xi, M.Cs.',
                'email' => 'dosen.xi@univ.ac.id',
                'nip' => '197802022003121014',
                'code' => 'D-XI',
                'academic_title' => 'Dr., M.Cs.',
                'department' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'username' => 'Dosen Omicron, M.T.',
                'email' => 'dosen.omicron@univ.ac.id',
                'nip' => '197603032002122015',
                'code' => 'D-OMI',
                'academic_title' => 'Dr., M.T.',
                'department' => 'Matematika Komputasi',
            ],
            [
                'username' => 'Dosen Pi, Ph.D.',
                'email' => 'dosen.pi@univ.ac.id',
                'nip' => '196504041990031016',
                'code' => 'D-PI',
                'academic_title' => 'Prof., Ph.D.',
                'department' => 'Statistika Terapan',
            ],
        ];

        foreach ($lecturers as $lecturer) {
            Lecturer::updateOrCreate(
                ['email' => $lecturer['email']],
                $lecturer
            );
        }
    }
}
