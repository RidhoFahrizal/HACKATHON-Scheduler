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

    // Leave slots 5, 8 and 9 open on each weekday; slots 6 and 7 cover lunch.
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
