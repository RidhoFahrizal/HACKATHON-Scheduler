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
                'code' => 'D4',
                'name' => 'Gedung D4',
                'floors_count' => 4,
                'rooms' => [
                    ['code' => 'C-102', 'name' => 'Ruang Workshop Komputer C-102', 'capacity' => 40, 'type' => 'lab', 'floor' => 1],
                    ['code' => 'C-103', 'name' => 'Laboratorium Jaringan & IoT', 'capacity' => 35, 'type' => 'lab', 'floor' => 1],
                    ['code' => 'C-104', 'name' => 'Laboratorium Data Science & AI', 'capacity' => 35, 'type' => 'lab', 'floor' => 1],
                    ['code' => 'C-105', 'name' => 'Laboratorium Rekayasa Perangkat Lunak', 'capacity' => 35, 'type' => 'lab', 'floor' => 1],
                    ['code' => 'D4-201', 'name' => 'Ruang Teori Multimedia D4-201', 'capacity' => 40, 'type' => 'teori', 'floor' => 2],
                    ['code' => 'D4-202', 'name' => 'Ruang Kuliah Komputasi D4-202', 'capacity' => 40, 'type' => 'teori', 'floor' => 2],
                    ['code' => 'D4-301', 'name' => 'Laboratorium Cyber Security', 'capacity' => 30, 'type' => 'lab', 'floor' => 3],
                ],
            ],
            [
                'code' => 'SAW',
                'name' => 'Gedung SAW',
                'floors_count' => 10,
                'rooms' => [
                    ['code' => 'SAW-05.02', 'name' => 'Ruang Diskusi & Seminar SAW-05.02', 'capacity' => 45, 'type' => 'teori', 'floor' => 5],
                    ['code' => 'SAW-06.10', 'name' => 'Ruang Kuliah Teori Pascasarjana SAW-06.10', 'capacity' => 40, 'type' => 'teori', 'floor' => 6],
                    ['code' => 'SAW-08', 'name' => 'Laboratorium Software Terpadu SAW-08', 'capacity' => 50, 'type' => 'lab', 'floor' => 8],
                    ['code' => 'SAW-09.01', 'name' => 'Ruang Riset Komputasi Lanjut', 'capacity' => 30, 'type' => 'lab', 'floor' => 9],
                ],
            ],
            [
                'code' => 'D3',
                'name' => 'Gedung D3',
                'floors_count' => 3,
                'rooms' => [
                    ['code' => 'B-101', 'name' => 'Ruang Laboratorium Bahasa B-101', 'capacity' => 35, 'type' => 'lab', 'floor' => 1],
                    ['code' => 'B-204', 'name' => 'Laboratorium Pemrosesan Sinyal B-204', 'capacity' => 35, 'type' => 'lab', 'floor' => 2],
                    ['code' => 'B-301', 'name' => 'Ruang Kuliah Telekomunikasi B-301', 'capacity' => 40, 'type' => 'teori', 'floor' => 3],
                ],
            ],
            [
                'code' => 'PASCA',
                'name' => 'Gedung Pasca',
                'floors_count' => 4,
                'rooms' => [
                    ['code' => 'Lab Riset Lt 3', 'name' => 'Laboratorium Riset Terapan', 'capacity' => 30, 'type' => 'lab', 'floor' => 3],
                    ['code' => 'PS-201', 'name' => 'Ruang Seminar Pascasarjana', 'capacity' => 60, 'type' => 'seminar', 'floor' => 2],
                ],
            ],
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
