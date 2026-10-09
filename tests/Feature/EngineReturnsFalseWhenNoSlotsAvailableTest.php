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

test('engine returns false when no good slots available', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang Kecil', 'capacity' => 10],
    ];

    $subjects = [['id' => 1, 'name' => 'Matematika', 'credits' => 2]];
    $students = $this->getStudents(30);
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

    dump('=== NO GOOD SLOTS (30 students, room capacity 10) ===');
    dump('Success: ' . ($result->success ? 'true' : 'false'));
    dump('Options: ' . count($result->options));

    expect($result->success)->toBeFalse();
    expect($result->options)->toBeEmpty();
});
