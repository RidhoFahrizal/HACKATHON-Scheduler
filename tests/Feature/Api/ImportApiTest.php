<?php

use App\Models\Building;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->building = Building::create(['code' => 'D4', 'name' => 'Gedung D4']);
});

test('can import rooms from json rows array', function () {
    $res = $this->postJson('/api/v1/import/rooms', [
        'rows' => [
            ['code' => 'C-201', 'name' => 'Lab AI', 'building_code' => 'D4', 'capacity' => '30', 'type' => 'lab', 'floor' => '2'],
            ['code' => 'C-202', 'name' => 'Lab Data', 'building_code' => 'D4', 'capacity' => '30', 'type' => 'lab', 'floor' => '2'],
        ],
    ])->assertOk();

    expect($res->json('imported'))->toBe(2)
        ->and(Room::where('code', 'C-201')->exists())->toBeTrue()
        ->and(Room::where('code', 'C-202')->exists())->toBeTrue();
});

test('can import subjects, lecturers, and students', function () {
    $this->postJson('/api/v1/import/subjects', [
        'rows' => [
            ['code' => 'SUB-1', 'name' => 'Pemrograman Web', 'sks' => '3', 'semester' => '3', 'department' => 'IT'],
        ],
    ])->assertOk();
    expect(Subject::where('code', 'SUB-1')->exists())->toBeTrue();

    $this->postJson('/api/v1/import/lecturers', [
        'rows' => [
            ['nip' => '19800001', 'name' => 'Dosen Baru', 'code' => 'DB', 'email' => 'db@pens.ac.id'],
        ],
    ])->assertOk();
    expect(Lecturer::where('nip', '19800001')->exists())->toBeTrue();

    $this->postJson('/api/v1/import/students', [
        'rows' => [
            ['nrp' => '31230001', 'name' => 'Mhs Baru', 'class' => '1 D4 IT A', 'cohort_year' => '2024'],
        ],
    ])->assertOk();
    expect(Student::where('nrp', '31230001')->exists())->toBeTrue();
});

test('can import from uploaded csv file', function () {
    $csv = "code,name,building_code,capacity,type,floor\n"
        . "R-TEST-1,Ruang Test 1,D4,25,teori,1\n";
    $file = UploadedFile::fake()->createWithContent('rooms.csv', $csv);

    $res = $this->postJson('/api/v1/import/rooms', [
        'file' => $file,
    ])->assertOk();

    expect($res->json('imported'))->toBe(1)
        ->and(Room::where('code', 'R-TEST-1')->exists())->toBeTrue();
});
