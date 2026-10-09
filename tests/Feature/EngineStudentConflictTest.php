<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\RoomDto;
use App\Domain\Scheduling\DTO\ScheduleDto;
use App\Domain\Scheduling\DTO\StudentSubjectDto;
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

test('engine detects student conflicts correctly', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = [new RoomDto(id: '1', name: 'Ruang A', capacity: 40)];

    $subjects = [
        new SubjectDto(id: '1', name: 'Matematika', credits: 2, lecturerId: '1'),
        new SubjectDto(id: '2', name: 'Fisika', credits: 2, lecturerId: '2'),
    ];

    $students = [
        ['id' => '101', 'name' => 'Student A'],
        ['id' => '102', 'name' => 'Student B'],
        ['id' => '103', 'name' => 'Student C'],
        ['id' => '104', 'name' => 'Student D'],
    ];

    $studentSubjects = [
        new StudentSubjectDto(id: '1', studentId: '101', subjectId: '1'),
        new StudentSubjectDto(id: '2', studentId: '102', subjectId: '1'),
        new StudentSubjectDto(id: '3', studentId: '103', subjectId: '1'),
        new StudentSubjectDto(id: '4', studentId: '104', subjectId: '1'),
        new StudentSubjectDto(id: '5', studentId: '101', subjectId: '2'),
        new StudentSubjectDto(id: '6', studentId: '102', subjectId: '2'),
    ];

    $schedules = [
        new ScheduleDto(
            id: '1',
            day: 0,
            startSlot: 0,
            endSlot: 1,
            subjectId: '1',
            lecturerId: '1',
            roomId: '1',
        ),
        new ScheduleDto(
            id: '2',
            day: 0,
            startSlot: 0,
            endSlot: 1,
            subjectId: '2',
            lecturerId: '2',
            roomId: '1',
        ),
    ];

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    dump('=== STUDENT CONFLICT DETECTION ===');
    dump('Students enrolled in target subject: 4');
    dump('Students also taking conflicting subject: 2 (101, 102)');
    dump('Conflicting schedule: day 0, slot 0-1 (same as candidate)');
    dump('');
    dump('Input details:');
    dump('  lecturer_schedules_count: ' . count($buildResult['input']->lecturerSchedules));
    dump('  student_schedules_count: ' . count($buildResult['input']->studentSchedules));
    dump('  studentIds: ' . json_encode($buildResult['input']->studentIds));
    dump('');
    dump('All options:');
    foreach ($result->options as $i => $option) {
        dump("  Option $i: {$option->day->label()} slot {$option->startSlot}-{$option->endSlot}");
    }
    dump('');
    dump('Options with conflict penalty:');

    $conflictFound = false;
    foreach ($result->options as $option) {
        if ($option->day->value === 0 && $option->startSlot === 0) {
            foreach ($option->codeFactors as $cf) {
                if ($cf->code === 1) {
                    $conflictFound = true;
                    dump("  Option: {$option->day->label()} {$option->startTime}-{$option->endTime}");
                    dump("    Code 1: {$cf->label} (-{$cf->penalty})");
                    dump("    Conflicted students: " . json_encode($cf->details['student_ids']));
                    dump("    Conflict count: {$cf->details['count']} / 4 students");
                    expect($cf->details['count'])->toBe(2);
                    expect($cf->details['student_ids'])->toContain('101');
                    expect($cf->details['student_ids'])->toContain('102');
                }
            }
        }
    }

    expect($conflictFound)->toBeTrue('Should find at least one option with student conflict');
});

test('engine calculates correct conflict penalty ratio', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = [new RoomDto(id: '1', name: 'Ruang A', capacity: 100)];

    $subjects = [
        new SubjectDto(id: '1', name: 'Matematika', credits: 2, lecturerId: '1'),
        new SubjectDto(id: '2', name: 'Fisika', credits: 2, lecturerId: '2'),
    ];

    $students = [];
    $studentSubjects = [];
    $id = 1;

    for ($i = 1; $i <= 10; $i++) {
        $studentId = (string) (100 + $i);
        $students[] = ['id' => $studentId, 'name' => "Student $i"];
        $studentSubjects[] = new StudentSubjectDto(id: (string) $id++, studentId: $studentId, subjectId: '1');
        if ($i <= 5) {
            $studentSubjects[] = new StudentSubjectDto(id: (string) $id++, studentId: $studentId, subjectId: '2');
        }
    }

    $schedules = [
        new ScheduleDto(
            id: '1',
            day: 0,
            startSlot: 0,
            endSlot: 1,
            subjectId: '1',
            lecturerId: '1',
            roomId: '1',
        ),
        new ScheduleDto(
            id: '2',
            day: 0,
            startSlot: 0,
            endSlot: 1,
            subjectId: '2',
            lecturerId: '2',
            roomId: '1',
        ),
    ];

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    dump('=== CONFLICT PENALTY RATIO ===');
    dump('Total students: 10');
    dump('Students in conflicting subject: 5');
    dump('Expected penalty: 50 * (5/10) = 25');

    foreach ($result->options as $option) {
        if ($option->day->value === 0 && $option->startSlot === 0) {
            foreach ($option->codeFactors as $cf) {
                if ($cf->code === 1) {
                    dump("Actual penalty: {$cf->penalty}");
                    expect($cf->penalty)->toBe(25);
                }
            }
        }
    }
});
