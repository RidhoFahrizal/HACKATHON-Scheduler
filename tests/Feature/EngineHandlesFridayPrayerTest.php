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

test('engine handles friday prayer time correctly', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-03-20',
    );

    $rooms = $this->getRooms(1, 40);
    $subjects = [['id' => 1, 'name' => 'Matematika', 'credits' => 2]];
    $students = $this->getStudents(1);
    $studentSubjects = $this->getStudentSubjects(1, $students);

    $schedules = [
        [
            'id' => 1,
            'day' => 4,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 1,
            'roomID' => 1,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    dump('=== FRIDAY PRAYER HANDLING ===');
    foreach ($result->options as $option) {
        if ($option->day->isFriday()) {
            dump("  Friday option: {$option->startTime}-{$option->endTime} (must end <= 11:20)");
            expect($option->endTime)->toBeLessThanOrEqual('11:20');
        }
    }
});
