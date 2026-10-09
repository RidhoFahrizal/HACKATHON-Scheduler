<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\Engine\SchedulingEngine;
use App\Domain\Scheduling\Enums\Scope;
use App\Models\AcademicCalendar;
use Tests\Feature\Traits\HasScheduleTestData;

uses(HasScheduleTestData::class);

beforeEach(function () {
    AcademicCalendar::create([
        'name' => 'Semester Ganjil 2026',
        'year' => 2026,
        'semester' => 'ganjil',
        'start_date' => '2026-02-09',
        'end_date' => '2026-06-01',
        'total_weeks' => 16,
        'is_active' => true,
    ]);

    $this->engine = new SchedulingEngine();
});

test('thinking log contains all steps', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(1, 40);
    $subjects = [['id' => 1, 'name' => 'Matematika', 'credits' => 2]];
    $students = $this->getStudents(10);
    $studentSubjects = $this->getStudentSubjects(1, $students);

    $schedules = [
        [
            'id' => 1,
            'day' => 0,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 5,
            'roomID' => 1,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    dump('=== THINKING LOG ===');
    dump($result->thinkingLog);

    expect($result->thinkingLog)->toContain('[STEP 1: Ambil bahan]');
    expect($result->thinkingLog)->toContain('[STEP 2: Bangun bitmask]');
    expect($result->thinkingLog)->toContain('[STEP 3: Cari jendela slot]');
    expect($result->thinkingLog)->toContain('[STEP 4: Filter ruang]');
    expect($result->thinkingLog)->toContain('[STEP 5: Scoring]');
});
