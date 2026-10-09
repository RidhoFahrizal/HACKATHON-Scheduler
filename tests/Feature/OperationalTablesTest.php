<?php

use App\Models\Building;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Lecturer;
use App\Models\RescheduleRequest;
use App\Models\Role;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\SystemAuditLog;
use App\Models\User;
use Illuminate\Support\Str;

test('users are assigned to a role through role_id', function () {
    $role = Role::create(['code' => 'baak', 'name' => 'BAAK'])->refresh();
    $user = User::factory()->create(['role_id' => $role->id]);

    expect(Str::isUuid($role->id))->toBeTrue()
        ->and($user->role->is($role))->toBeTrue()
        ->and($role->users)->toHaveCount(1)
        ->and($role->is_active)->toBeTrue();
});

test('a reschedule request links schedule, requester, target room, and reviewer', function () {
    $building = Building::create(['code' => 'D4', 'name' => 'Gedung D4']);
    $room = Room::create(['building_id' => $building->id, 'code' => 'D4-101', 'name' => 'Ruang 101', 'capacity' => 40]);
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
    $requester = User::factory()->create();
    $reviewer = User::factory()->create();

    $request = RescheduleRequest::create([
        'request_code' => 'REQ-01',
        'schedule_id' => $schedule->id,
        'requester_id' => $requester->id,
        'target_date' => '2026-10-14',
        'target_day' => 'Rabu',
        'target_start_time' => '13:00',
        'target_end_time' => '16:00',
        'target_room_id' => $room->id,
        'reason' => 'Bentrok jadwal ujian sertifikasi',
        'reviewed_by' => $reviewer->id,
    ])->refresh();

    expect(Str::isUuid($request->id))->toBeTrue()
        ->and($request->status)->toBe('pending')
        ->and($request->duration_type)->toBe('1_minggu')
        ->and($request->urgency)->toBe('Normal')
        ->and($request->target_date->toDateString())->toBe('2026-10-14')
        ->and($request->schedule->is($schedule))->toBeTrue()
        ->and($request->requester->is($requester))->toBeTrue()
        ->and($request->targetRoom->is($room))->toBeTrue()
        ->and($request->reviewer->is($reviewer))->toBeTrue();
});

test('audit log entries keep the actor name when no user is linked', function () {
    $log = SystemAuditLog::create([
        'actor_name' => 'Sistem Otomatis',
        'module' => 'Integritas Jadwal',
        'action' => 'integrity_check',
    ])->refresh();

    expect(Str::isUuid($log->id))->toBeTrue()
        ->and($log->level)->toBe('info')
        ->and($log->status)->toBe('Sukses')
        ->and($log->actor)->toBeNull()
        ->and($log->timestamp)->not->toBeNull();
});

test('chat messages belong to a session and cast slot suggestions to an array', function () {
    $session = ChatSession::create(['user_id' => User::factory()->create()->id])->refresh();

    $message = $session->messages()->create([
        'sender_type' => 'ai',
        'message' => 'Berikut rekomendasi slot.',
        'suggested_slots_payload' => [['day' => 'Rabu', 'time' => '13:00 - 16:00', 'room' => 'Lab C 103']],
    ]);

    expect($session->title)->toBe('Konsultasi Jadwal')
        ->and($message->refresh()->suggested_slots_payload[0]['room'])->toBe('Lab C 103')
        ->and(ChatMessage::first()->session->is($session))->toBeTrue();
});

test('deleting a chat session removes its messages', function () {
    $session = ChatSession::create(['user_id' => User::factory()->create()->id]);
    $session->messages()->create(['sender_type' => 'user', 'message' => 'Halo']);

    $session->delete();

    expect(ChatMessage::count())->toBe(0);
});
