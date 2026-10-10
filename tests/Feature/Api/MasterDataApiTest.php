<?php

use App\Models\Building;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;

test('can list, create, update, and soft delete rooms', function () {
    $building = Building::create(['code' => 'D4', 'name' => 'Gedung D4']);

    $res = $this->postJson('/api/v1/rooms', [
        'code' => 'C-101',
        'name' => 'Lab Jaringan',
        'building_code' => 'D4',
        'capacity' => 30,
        'type' => 'lab',
        'floor' => 1,
    ])->assertCreated();

    $roomId = $res->json('data.id');
    expect($roomId)->not->toBeNull();

    $this->getJson('/api/v1/rooms')
        ->assertOk()
        ->assertJsonFragment(['code' => 'C-101']);

    $this->putJson("/api/v1/rooms/{$roomId}", [
        'code' => 'C-101',
        'name' => 'Lab Jaringan Komputer',
        'building_code' => 'D4',
        'capacity' => 35,
    ])->assertOk()->assertJsonPath('data.name', 'Lab Jaringan Komputer');

    $this->deleteJson("/api/v1/rooms/{$roomId}")->assertNoContent();
    expect(Room::find($roomId))->toBeNull();
    expect(Room::withTrashed()->find($roomId))->not->toBeNull();
});

test('cannot delete room if referenced in active schedule', function () {
    $building = Building::create(['code' => 'D4', 'name' => 'Gedung D4']);
    $room = Room::create(['building_id' => $building->id, 'code' => 'R-1', 'name' => 'Ruang 1', 'capacity' => 30]);
    $lecturer = Lecturer::create(['username' => 'Dosen A', 'email' => 'a@pens.ac.id', 'nip' => '111']);
    $subject = Subject::create(['code' => 'S-1', 'name' => 'Subjek 1', 'credits' => 3, 'lecturerId' => $lecturer->id]);
    Schedule::create([
        'day' => 0, 'startSlot' => 0, 'endSlot' => 2,
        'subjectId' => $subject->id, 'lecturerId' => $lecturer->id,
        'roomId' => $room->id, 'semesterType' => 'ganjil',
    ]);

    $this->deleteJson("/api/v1/rooms/{$room->id}")
        ->assertStatus(409)
        ->assertJsonStructure(['message']);
});

test('can manage subjects with sks alias mapping', function () {
    $res = $this->postJson('/api/v1/subjects', [
        'code' => 'MK-01',
        'name' => 'Struktur Data',
        'sks' => 3,
        'semester' => 2,
        'department' => 'Teknik Informatika',
    ])->assertCreated();

    $id = $res->json('data.id');
    expect($res->json('data.credits'))->toBe(3);

    $this->putJson("/api/v1/subjects/{$id}", [
        'code' => 'MK-01',
        'name' => 'Struktur Data Lanjut',
        'sks' => 4,
    ])->assertOk()->assertJsonPath('data.credits', 4);

    $this->deleteJson("/api/v1/subjects/{$id}")->assertNoContent();
});

test('can manage lecturers and students', function () {
    $lecturerRes = $this->postJson('/api/v1/lecturers', [
        'name' => 'Dr. Budi',
        'email' => 'budi@pens.ac.id',
        'nip' => '19800101',
        'code' => 'BS',
    ])->assertCreated();

    $lecturerId = $lecturerRes->json('data.id');
    expect($lecturerRes->json('data.username'))->toBe('Dr. Budi');

    $studentRes = $this->postJson('/api/v1/students', [
        'name' => 'Realdho',
        'class' => '3 D4 IT A',
        'nrp' => '12345678',
        'cohort_year' => 2023,
    ])->assertCreated();

    $studentId = $studentRes->json('data.id');
    expect($studentRes->json('data.username'))->toBe('Realdho');

    $this->deleteJson("/api/v1/lecturers/{$lecturerId}")->assertNoContent();
    $this->deleteJson("/api/v1/students/{$studentId}")->assertNoContent();
});
