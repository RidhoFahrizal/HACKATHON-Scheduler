<?php

use App\Models\Building;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;

test('catalog API includes room building data for the frontend', function () {
    $building = Building::create([
        'code' => 'D4',
        'name' => 'Gedung D4',
        'floors_count' => 4,
    ]);

    Room::create([
        'building_id' => $building->id,
        'code' => 'D4-101',
        'name' => 'Ruang 101',
        'capacity' => 35,
        'type' => 'teori',
        'floor' => 1,
        'is_active' => true,
    ]);

    $this->getJson('/api/scheduling/catalog')
        ->assertOk()
        ->assertJsonPath('buildings.0.code', 'D4')
        ->assertJsonPath('rooms.0.code', 'D4-101')
        ->assertJsonPath('rooms.0.building.name', 'Gedung D4')
        ->assertJsonPath('schedules', []);
});

test('evaluation endpoint requires an existing database schedule UUID', function () {
    $this->postJson('/api/scheduling/evaluate', [
        'scheduleId' => '00000000-0000-4000-8000-000000000099',
        'scope' => 'once',
        'target' => now()->toDateString(),
    ])->assertUnprocessable()->assertJsonValidationErrors('scheduleId');
});

test('frontend evaluation endpoint runs the engine for a persisted schedule', function () {
    $building = Building::create(['code' => 'D4', 'name' => 'Gedung D4']);
    $room = Room::create([
        'building_id' => $building->id,
        'code' => 'D4-101',
        'name' => 'Ruang 101',
        'capacity' => 40,
    ]);
    $lecturer = Lecturer::create(['username' => 'Dosen Test', 'email' => 'dosen@example.test']);
    $subject = Subject::create(['name' => 'Matematika', 'credits' => 2, 'lecturerId' => $lecturer->id]);
    $schedule = Schedule::create([
        'day' => 0,
        'startSlot' => 0,
        'endSlot' => 1,
        'subjectId' => $subject->id,
        'lecturerId' => $lecturer->id,
        'roomId' => $room->id,
        'semesterType' => 'ganjil',
    ]);

    $this->postJson('/api/scheduling/evaluate', [
        'scheduleId' => $schedule->id,
        'scope' => 'once',
        'target' => now()->toDateString(),
    ])->assertOk()
        ->assertJsonStructure(['success', 'steps', 'options'])
        ->assertJsonPath('options.0.room_id', $room->id);
});
