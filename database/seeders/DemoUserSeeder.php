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
        $baakRole = Role::where('code', 'baak')->first() ?? $staffRole;

        $users = [
            // PENSCEDULER Demo Active Role Users (Synthetic Names)
            [
                'name' => 'Mahasiswa Alpha',
                'email' => 'mhs.alpha@student.pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $mahasiswaRole->id,
            ],
            [
                'name' => 'Dosen Alpha, S.Kom., M.T.',
                'email' => 'dosen.alpha@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Petugas BAAK Kampus',
                'email' => 'baak@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $baakRole->id,
            ],

            // Other Academic Users (Synthetic Fictional Names)
            [
                'name' => 'Admin Sistem Kampus',
                'email' => 'admin@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ],
            [
                'name' => 'Dosen Kaprodi, M.T.',
                'email' => 'kaprodi@univ.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $kaprodiRole->id,
            ],
            [
                'name' => 'Dosen Beta, S.Kom., M.T.',
                'email' => 'dosen.beta@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dosen Gamma, S.Kom., M.T.',
                'email' => 'dosen.gamma@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dosen Delta, S.ST., M.Tr.Kom.',
                'email' => 'dosen.delta@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dosen Epsilon, S.Kom., M.Kom.',
                'email' => 'dosen.epsilon@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dosen Zeta, Ph.D.',
                'email' => 'dosen.zeta@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Dosen Eta, Ph.D.',
                'email' => 'dosen.eta@pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $dosenRole->id,
            ],
            [
                'name' => 'Mahasiswa Beta',
                'email' => 'mhs.beta@student.pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $mahasiswaRole->id,
            ],
            [
                'name' => 'Mahasiswa Gamma',
                'email' => 'mhs.gamma@student.pens.ac.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $mahasiswaRole->id,
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
