<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Services\DemoActorResolver;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DemoActorResolver::ROLES as $code => $profile) {
            Role::updateOrCreate(['code' => $code], ['name' => $profile['label']]);
        }
    }
}
