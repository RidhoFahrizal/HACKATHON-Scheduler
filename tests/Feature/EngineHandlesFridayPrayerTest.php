<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\RoomDto;
use App\Domain\Scheduling\DTO\ScheduleDto;
use App\Domain\Scheduling\DTO\SubjectDto;
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

    $this->engine = new SchedulingEngine;
});

test('engine handles friday prayer time correctly', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-20',
    );

    $rooms = [new RoomDto(id: '1', name: 'Ruang A', capacity: 40)];
    $subjects = [new SubjectDto(id: '1', name: 'Matematika', credits: 2, lecturerId: '1')];
    $students = $this->getStudents(1);
    $studentSubjects = $this->getStudentSubjects('1', $students);

    $schedules = [
        new ScheduleDto(
            id: '1',
            day: 4,
            startSlot: 0,
            endSlot: 1,
            subjectId: '1',
            lecturerId: '1',
            roomId: '1',
        ),
    ];

    foreach ([0, 1, 2, 3, 5, 6] as $day) {
        $schedules[] = new ScheduleDto(
            id: 'blocker-'.$day,
            day: $day,
            startSlot: 0,
            endSlot: 14,
            subjectId: '99',
            lecturerId: '1',
            roomId: '99',
        );
    }

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    dump('=== FRIDAY PRAYER HANDLING ===');
    $fridayOptions = array_values(array_filter($result->options, fn ($option) => $option->day->isFriday()));

    expect($fridayOptions)->not->toBeEmpty();
    foreach ($fridayOptions as $option) {
        expect($option->endTime)->toBeLessThanOrEqual('11:20');
    }
});
