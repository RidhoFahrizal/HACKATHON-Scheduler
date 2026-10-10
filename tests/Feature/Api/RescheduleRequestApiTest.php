<?php

use App\Models\Building;
use App\Models\Lecturer;
use App\Models\RescheduleRequest;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;

beforeEach(function () {
    $this->seed(\Database\Seeders\SchedulingConfigSeeder::class);
    $this->building = Building::create(['code' => 'D4', 'name' => 'Gedung D4']);
    $this->room1 = Room::create(['building_id' => $this->building->id, 'code' => 'R-1', 'name' => 'Lab 1', 'capacity' => 30]);
    $this->room2 = Room::create(['building_id' => $this->building->id, 'code' => 'R-2', 'name' => 'Lab 2', 'capacity' => 30]);
    $this->lecturer = Lecturer::create(['username' => 'Dosen Test', 'email' => 'dosen@example.test', 'nip' => '12345']);
    $this->subject = Subject::create(['code' => 'MK-1', 'name' => 'Matematika', 'credits' => 2, 'lecturerId' => $this->lecturer->id]);
    $this->schedule = Schedule::create([
        'day' => 0, 'startSlot' => 0, 'endSlot' => 1,
        'subjectId' => $this->subject->id, 'lecturerId' => $this->lecturer->id,
        'roomId' => $this->room1->id, 'semesterType' => 'ganjil',
    ]);
});

test('student submit creates a pending reschedule request', function () {
    $this->withSession(['role' => 'mahasiswa']);

    $res = $this->postJson('/api/v1/reschedule-requests', [
        'scheduleId' => $this->schedule->id,
        'targetDate' => now()->addDays(2)->toDateString(),
        'targetDay' => 'Rabu',
        'targetStartTime' => '08:00',
        'targetEndTime' => '09:40',
        'targetRoomId' => $this->room2->id,
        'durationType' => '1_minggu',
        'reason' => 'Ada acara lomba',
    ])->assertCreated();

    expect($res->json('data.status'))->toBe('Menunggu Persetujuan Dosen');
    expect($res->json('data.statusCode'))->toBe('pending');
});

test('dosen submit auto-approves request and creates booking', function () {
    $this->withSession(['role' => 'dosen']);

    $res = $this->postJson('/api/v1/reschedule-requests', [
        'scheduleId' => $this->schedule->id,
        'targetDate' => now()->addDays(2)->toDateString(),
        'targetDay' => 'Rabu',
        'targetStartTime' => '08:00',
        'targetEndTime' => '09:40',
        'targetRoomId' => $this->room2->id,
        'reason' => 'Tugas dinas',
    ])->assertCreated();

    expect($res->json('data.status'))->toBe('Disetujui');
    expect($res->json('data.statusCode'))->toBe('approved');
});

test('dosen can approve or reject pending requests', function () {
    $req = RescheduleRequest::create([
        'request_code' => 'REQ-0001',
        'schedule_id' => $this->schedule->id,
        'requester_id' => \App\Models\User::factory()->create()->id,
        'target_date' => now()->addDays(3)->toDateString(),
        'target_day' => 'Kamis',
        'target_start_time' => '08:00',
        'target_end_time' => '09:40',
        'target_room_id' => $this->room2->id,
        'reason' => 'Ujian sertifikasi',
        'status' => 'pending',
    ]);

    // Mahasiswa cannot approve
    $this->withSession(['role' => 'mahasiswa'])
        ->postJson("/api/v1/reschedule-requests/{$req->id}/approve")
        ->assertForbidden();

    // Dosen can approve
    $this->withSession(['role' => 'dosen'])
        ->postJson("/api/v1/reschedule-requests/{$req->id}/approve")
        ->assertOk()
        ->assertJsonPath('data.status', 'Disetujui');
});
