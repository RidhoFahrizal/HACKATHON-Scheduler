<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'code' => 'admin',
                'name' => 'Administrator',
                'description' => 'Administrator sistem dengan akses penuh ke seluruh fitur aplikasi',
                'is_active' => true,
            ],
            [
                'code' => 'kaprodi',
                'name' => 'Kepala Program Studi',
                'description' => 'Kepala program studi yang mengelola jadwal perkuliahan dan persetujuan',
                'is_active' => true,
            ],
            [
                'code' => 'dosen',
                'name' => 'Dosen',
                'description' => 'Dosen pengampu mata kuliah yang dapat melihat jadwal mengajar',
                'is_active' => true,
            ],
            [
                'code' => 'mahasiswa',
                'name' => 'Mahasiswa',
                'description' => 'Mahasiswa yang dapat melihat jadwal kuliah dan mengajukan reschedule',
                'is_active' => true,
            ],
            [
                'code' => 'staff',
                'name' => 'Staff Tata Usaha',
                'description' => 'Staff TU yang membantu pengelolaan jadwal dan ruangan',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['code' => $role['code']],
                $role
            );
        }
    }
}
