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

test('engine filters rooms by capacity', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang Kecil', 'capacity' => 20],
        ['id' => 2, 'name' => 'Ruang Besar', 'capacity' => 40],
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

    dump('=== ROOM CAPACITY FILTER (30 students, rooms: 20 & 40) ===');
    foreach ($result->options as $option) {
        dump("  Room: {$option->roomName} (capacity >= 30)");
        expect($option->roomName)->toBe('Ruang Besar');
    }
});
