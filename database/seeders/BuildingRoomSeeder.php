<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Database\Seeder;

class BuildingRoomSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            [
                'code' => 'GA',
                'name' => 'Gedung A - Gedung Utama',
                'floors_count' => 4,
                'rooms' => [
                    ['code' => 'GA-101', 'name' => 'Ruang Kuliah 101', 'capacity' => 40, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GA-102', 'name' => 'Ruang Kuliah 102', 'capacity' => 40, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GA-201', 'name' => 'Ruang Kuliah 201', 'capacity' => 50, 'type' => 'teori', 'floor' => 2],
                    ['code' => 'GA-202', 'name' => 'Ruang Kuliah 202', 'capacity' => 50, 'type' => 'teori', 'floor' => 2],
                    ['code' => 'GA-301', 'name' => 'Ruang Seminar 301', 'capacity' => 80, 'type' => 'seminar', 'floor' => 3],
                    ['code' => 'GA-401', 'name' => 'Ruang Rapat Pimpinan', 'capacity' => 20, 'type' => 'rapat', 'floor' => 4],
                ],
            ],
            [
                'code' => 'GB',
                'name' => 'Gedung B - Fakultas Teknik',
                'floors_count' => 3,
                'rooms' => [
                    ['code' => 'GB-101', 'name' => 'Ruang Kuliah Teknik 101', 'capacity' => 45, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GB-102', 'name' => 'Ruang Kuliah Teknik 102', 'capacity' => 45, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GB-201', 'name' => 'Laboratorium Komputer 1', 'capacity' => 30, 'type' => 'lab', 'floor' => 2],
                    ['code' => 'GB-202', 'name' => 'Laboratorium Komputer 2', 'capacity' => 30, 'type' => 'lab', 'floor' => 2],
                    ['code' => 'GB-301', 'name' => 'Laboratorium Jaringan', 'capacity' => 25, 'type' => 'lab', 'floor' => 3],
                    ['code' => 'GB-302', 'name' => 'Laboratorium IoT', 'capacity' => 20, 'type' => 'lab', 'floor' => 3],
                ],
            ],
            [
                'code' => 'GC',
                'name' => 'Gedung C - Fakultas Sains',
                'floors_count' => 4,
                'rooms' => [
                    ['code' => 'GC-101', 'name' => 'Ruang Kuliah Sains 101', 'capacity' => 60, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GC-102', 'name' => 'Ruang Kuliah Sains 102', 'capacity' => 60, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GC-201', 'name' => 'Laboratorium Fisika', 'capacity' => 25, 'type' => 'lab', 'floor' => 2],
                    ['code' => 'GC-202', 'name' => 'Laboratorium Kimia', 'capacity' => 25, 'type' => 'lab', 'floor' => 2],
                    ['code' => 'GC-301', 'name' => 'Laboratorium Biologi', 'capacity' => 25, 'type' => 'lab', 'floor' => 3],
                    ['code' => 'GC-401', 'name' => 'Aula Serbaguna', 'capacity' => 150, 'type' => 'aula', 'floor' => 4],
                ],
            ],
            [
                'code' => 'GD',
                'name' => 'Gedung D - Gedung Bersama',
                'floors_count' => 2,
                'rooms' => [
                    ['code' => 'GD-101', 'name' => 'Ruang Kuliah Umum 101', 'capacity' => 100, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GD-102', 'name' => 'Ruang Kuliah Umum 102', 'capacity' => 100, 'type' => 'teori', 'floor' => 1],
                    ['code' => 'GD-201', 'name' => 'Ruang Studio Multimedia', 'capacity' => 35, 'type' => 'studio', 'floor' => 2],
                    ['code' => 'GD-202', 'name' => 'Ruang Praktik Desain', 'capacity' => 30, 'type' => 'praktikum', 'floor' => 2],
                ],
            ],
        ];

        foreach ($buildings as $buildingData) {
            $rooms = $buildingData['rooms'];
            unset($buildingData['rooms']);

            $building = Building::updateOrCreate(
                ['code' => $buildingData['code']],
                $buildingData
            );

            foreach ($rooms as $room) {
                Room::updateOrCreate(
                    ['code' => $room['code']],
                    array_merge($room, [
                        'building_id' => $building->id,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
