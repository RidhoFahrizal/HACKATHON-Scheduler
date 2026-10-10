<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('code', 'admin')->first();
        $kaprodiRole = Role::where('code', 'kaprodi')->first();
        $dosenRole = Role::where('code', 'dosen')->first();
        $mahasiswaRole = Role::where('code', 'mahasiswa')->first();
        $staffRole = Role::where('code', 'staff')->first();

        $users = [
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ],
            [
                'name' => 'Dr. Ir. Hendra Gunawan, M.T.',
                'email' => 'kaprodi@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $kaprodiRole->id,
            ],
            [
                'name' => 'Dr. Budi Santoso, M.Kom.',
                'email' => 'budi.santoso@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Prof. Dr. Siti Rahayu, M.Sc.',
                'email' => 'siti.rahayu@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dr. Ahmad Wijaya, M.T.',
                'email' => 'ahmad.wijaya@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dr. Linda Kusuma, M.Kom.',
                'email' => 'linda.kusuma@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Ir. Rudi Hartono, M.Eng.',
                'email' => 'rudi.hartono@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dr. Maya Putri, M.Si.',
                'email' => 'maya.putri@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dr. Fajar Nugroho, M.Cs.',
                'email' => 'fajar.nugroho@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dr. Ani Suryani, M.T.',
                'email' => 'ani.suryani@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Prof. Bambang Irawan, Ph.D.',
                'email' => 'bambang.irawan@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $staffRole->id,
            ],
            [
                'name' => 'Rina Wulandari',
                'email' => 'rina.wulandari@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $staffRole->id,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
