<?php

use App\Models\Building;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->seed(\Database\Seeders\SchedulingConfigSeeder::class);
    $this->building = Building::create(['code' => 'D4', 'name' => 'Gedung D4']);
    $this->room = Room::create(['building_id' => $this->building->id, 'code' => 'R-1', 'name' => 'Lab 1', 'capacity' => 30]);
    $this->lecturer = Lecturer::create(['username' => 'Dosen Test', 'email' => 'dosen@example.test', 'nip' => '12345']);
    $this->subject = Subject::create(['code' => 'MK-1', 'name' => 'Matematika', 'credits' => 2, 'lecturerId' => $this->lecturer->id]);
    $this->schedule = Schedule::create([
        'day' => 0, 'startSlot' => 0, 'endSlot' => 1,
        'subjectId' => $this->subject->id, 'lecturerId' => $this->lecturer->id,
        'roomId' => $this->room->id, 'semesterType' => 'ganjil',
    ]);
});

test('chat message with schedule evaluates bitmask engine and calls llm or template', function () {
    Http::fake([
        'api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'Berikut adalah opsi rekomendasi waktu bebas bentrok dari engine.']],
            ],
        ]),
    ]);

    config(['services.ai_chat.api_key' => 'fake-key']);

    $res = $this->postJson('/api/v1/chat/message', [
        'message' => 'Cari slot kosong untuk kuliah Matematika',
        'scheduleId' => $this->schedule->id,
        'scope' => 'once',
    ])->assertOk();

    expect($res->json('engineRan'))->toBeTrue()
        ->and($res->json('sessionId'))->not->toBeNull()
        ->and($res->json('reply'))->toContain('rekomendasi');
});

test('chat message without schedule runs fallback conversational mode', function () {
    $res = $this->postJson('/api/v1/chat/message', [
        'message' => 'Halo apa kabar?',
    ])->assertOk();

    expect($res->json('engineRan'))->toBeFalse()
        ->and($res->json('slots'))->toBeArray()
        ->and($res->json('reply'))->not->toBeEmpty();
});
