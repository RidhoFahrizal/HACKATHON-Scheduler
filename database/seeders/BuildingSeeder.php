<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            ['code' => 'D4', 'name' => 'Gedung D4', 'floors_count' => 4],
            ['code' => 'D3', 'name' => 'Gedung D3', 'floors_count' => 4],
            ['code' => 'PASCA', 'name' => 'Gedung Pasca', 'floors_count' => 8],
            ['code' => 'SAW', 'name' => 'Gedung SAW', 'floors_count' => 8],
        ];

        foreach ($buildings as $building) {
            Building::updateOrCreate(['code' => $building['code']], $building);
        }
    }
}
