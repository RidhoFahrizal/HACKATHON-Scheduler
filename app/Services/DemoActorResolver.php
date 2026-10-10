<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * The app has no authentication yet, so the role picked in the role switcher is the only identity signal.
 * Demo users are created lazily to satisfy the requester and reviewer foreign keys.
 */
final class DemoActorResolver
{
    public const ROLES = [
        'mahasiswa' => ['label' => 'Mahasiswa', 'name' => 'Realdho Fahryz', 'email' => 'realdho@it.student.pens.ac.id'],
        'dosen' => ['label' => 'Dosen', 'name' => 'Dr. Ir. Budi Sxxxx, M.T.', 'email' => 'budi.sxxxx@pens.ac.id'],
        'baak' => ['label' => 'BAAK', 'name' => 'Biro Administrasi Akademik', 'email' => 'baak@pens.ac.id'],
    ];

    public function roleFor(Request $request): string
    {
        $role = $request->hasSession() ? $request->session()->get('role', 'mahasiswa') : 'mahasiswa';

        return array_key_exists($role, self::ROLES) ? $role : 'mahasiswa';
    }

    public function userFor(string $role): User
    {
        $profile = self::ROLES[$role];
        $roleModel = Role::firstOrCreate(['code' => $role], ['name' => $profile['label']]);

        $user = User::firstOrNew(['email' => $profile['email']]);
        if (! $user->exists) {
            $user->forceFill([
                'name' => $profile['name'],
                'password' => Str::random(40),
            ]);
        }
        $user->role_id = $roleModel->id;
        $user->save();

        return $user;
    }

    public function label(string $role): string
    {
        return self::ROLES[$role]['label'] ?? 'Mahasiswa';
    }
}
