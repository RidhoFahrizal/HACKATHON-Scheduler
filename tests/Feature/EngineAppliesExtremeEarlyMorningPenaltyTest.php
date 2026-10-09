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

    $this->engine = new SchedulingEngine();
});

test('engine applies extreme early morning penalty when normal slots are blocked', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = [new RoomDto(id: '1', name: 'Ruang A', capacity: 40)];
    $subjects = [new SubjectDto(id: '1', name: 'Matematika', credits: 2, lecturerId: '5')];
    $students = $this->getStudents(10);
    $studentSubjects = $this->getStudentSubjects('1', $students);

    $schedules = [
        new ScheduleDto(
            id: '1',
            day: 0,
            startSlot: 0,
            endSlot: 1,
            subjectId: '1',
            lecturerId: '5',
            roomId: '1',
        ),
    ];

    $blockerId = 2;
    for ($day = 0; $day <= 6; $day++) {
        for ($slot = 2; $slot <= 14; $slot++) {
            $schedules[] = new ScheduleDto(
                id: 'blocker_' . $blockerId++,
                day: $day,
                startSlot: $slot,
                endSlot: $slot,
                subjectId: '99',
                lecturerId: '99',
                roomId: '1',
            );
        }
    }

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    $extremeEarlyPenaltyApplied = false;

    foreach ($result->options as $option) {
        // cek opsi slot 0 (jam 07:00)
        if ($option->startSlot === 0) {
            foreach ($option->codeFactors as $cf) {
                if ($cf->code === 4) {
                    expect($cf->label)->toBe('Jam ekstrem terlalu pagi');
                    expect($cf->penalty)->toBeGreaterThan(15);
                    $extremeEarlyPenaltyApplied = true;
                }
            }
        }
    }

    expect($extremeEarlyPenaltyApplied)->toBeTrue();
});

test('engine applies extreme late afternoon penalty when normal slots are blocked', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = [new RoomDto(id: '1', name: 'Ruang A', capacity: 40)];
    $subjects = [new SubjectDto(id: '1', name: 'Matematika', credits: 2, lecturerId: '5')];
    $students = $this->getStudents(10);
    $studentSubjects = $this->getStudentSubjects('1', $students);

    $schedules = [
        new ScheduleDto(
            id: '1',
            day: 0,
            startSlot: 0,
            endSlot: 1,
            subjectId: '1',
            lecturerId: '5',
            roomId: '1',
        ),
    ];

    // block ruang a dari slot 0 (07:00) sampai slot 11 (17:00) di semua hari
    // biar sisa slot malam doang yang kosong
    $blockerId = 2;
    for ($day = 0; $day <= 6; $day++) {
        for ($slot = 0; $slot <= 11; $slot++) {
            $schedules[] = new ScheduleDto(
                id: 'blocker_' . $blockerId++,
                day: $day,
                startSlot: $slot,
                endSlot: $slot,
                subjectId: '99',
                lecturerId: '99',
                roomId: '1',
            );
        }
    }

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    $extremeLatePenaltyApplied = false;

    foreach ($result->options as $option) {
        foreach ($option->codeFactors as $cf) {
            // cek kalau penalti ekstrem malam ke-trigger
            if ($cf->code === 5 && $cf->label === 'Jam ekstrem malam') {
                expect($cf->penalty)->toBeGreaterThan(15);
                $extremeLatePenaltyApplied = true;
            }
        }
    }

    expect($extremeLatePenaltyApplied)->toBeTrue();
});