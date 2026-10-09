<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\ScheduleDto;
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

test('engine handles slot jumping over lunch break', function () {
    $request = new RescheduleRequest(
        scheduleId: '3',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(3, 40);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(2);

    $studentSubjects = array_merge(
        $this->getStudentSubjects('1', $students),
        $this->getStudentSubjects('2', $students),
        $this->getStudentSubjects('3', $students),
    );

    $schedules = $this->getBaseSchedules();

    $buildResult = $this->buildEngineInput('3', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    dump('=== SLOT JUMPING (3 SKS = 3 slots, jumps over lunch) ===');
    dump('Thinking Log:');
    dump($result->thinkingLog);
    dump('Options:');
    foreach ($result->options as $i => $option) {
        dump('  #'.($i + 1)." {$option->day->label()} {$option->startTime}-{$option->endTime} | {$option->roomName} | Score: {$option->score}");
        foreach ($option->codeFactors as $cf) {
            dump("      Code {$cf->code}: {$cf->label} (-{$cf->penalty})");
        }
    }

    expect($result->thinkingLog)->toContain('Slot jumping');
    expect($result->options)->toBeArray();
});

test('onwards scope keeps slot-jumping options when teaching slots are free', function () {
    $request = new RescheduleRequest(
        scheduleId: '3',
        scope: Scope::ONWARDS,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(1, 40);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(2);
    $studentSubjects = $this->getStudentSubjects('3', $students);
    $schedules = $this->getBaseSchedules();

    // Leave teaching slots on both sides of the single lunch slot.
    $blockerId = 10;
    foreach (range(0, 6) as $day) {
        $schedules[] = new ScheduleDto(
            id: (string) $blockerId++,
            day: $day,
            startSlot: 0,
            endSlot: 4,
            subjectId: '99',
            lecturerId: '3',
            roomId: '99',
        );
        $schedules[] = new ScheduleDto(
            id: (string) $blockerId++,
            day: $day,
            startSlot: 10,
            endSlot: 14,
            subjectId: '99',
            lecturerId: '3',
            roomId: '99',
        );
    }

    $buildResult = $this->buildEngineInput('3', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);
    $hasSlotJumpingOption = collect($result->options)
        ->contains(fn ($option) => collect($option->codeFactors)->contains(fn ($factor) => $factor->code === 7));

    expect($hasSlotJumpingOption)->toBeTrue();
});

test('slot-jumping candidate never starts inside the lunch break', function () {
    $request = new RescheduleRequest(
        scheduleId: '3',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(1, 40);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(2);
    $studentSubjects = $this->getStudentSubjects('3', $students);
    $schedules = $this->getBaseSchedules();

    // Keep only the lunch slot and teaching slots after it free.
    $blockerId = 20;
    foreach (range(0, 6) as $day) {
        $schedules[] = new ScheduleDto(
            id: (string) $blockerId++,
            day: $day,
            startSlot: 0,
            endSlot: 5,
            subjectId: '99',
            lecturerId: '3',
            roomId: '99',
        );
        $schedules[] = new ScheduleDto(
            id: (string) $blockerId++,
            day: $day,
            startSlot: 12,
            endSlot: 14,
            subjectId: '99',
            lecturerId: '3',
            roomId: '99',
        );
    }

    $buildResult = $this->buildEngineInput('3', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    expect(array_filter($result->options, fn ($option) => in_array($option->startTime, ['12:00', '12:50'], true)))->toBeEmpty();
});

test('slot jumping treats lunch as one hour and resumes at 13:00', function () {
    $request = new RescheduleRequest(
        scheduleId: '3',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(1, 40);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(2);
    $studentSubjects = $this->getStudentSubjects('3', $students);
    $schedules = $this->getBaseSchedules();

    $blockerId = 30;
    foreach (range(0, 6) as $day) {
        $ranges = $day === 0 ? [[0, 4], [9, 14]] : [[0, 14]];
        foreach ($ranges as [$startSlot, $endSlot]) {
            $schedules[] = new ScheduleDto(
                id: (string) $blockerId++,
                day: $day,
                startSlot: $startSlot,
                endSlot: $endSlot,
                subjectId: '99',
                lecturerId: '3',
                roomId: '99',
            );
        }
    }

    $buildResult = $this->buildEngineInput('3', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);
    $jumpFactor = collect($result->options[0]->codeFactors)->first(fn ($factor) => $factor->code === 7);

    expect($result->options)->toHaveCount(1)
        ->and($result->options[0]->startTime)->toBe('11:10')
        ->and($result->options[0]->endTime)->toBe('14:40')
        ->and($jumpFactor)->not->toBeNull()
        ->and($jumpFactor->details['count'])->toBe(1);
});
