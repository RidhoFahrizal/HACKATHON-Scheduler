<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
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
        dump("  #" . ($i + 1) . " {$option->day->label()} {$option->startTime}-{$option->endTime} | {$option->roomName} | Score: {$option->score}");
        foreach ($option->codeFactors as $cf) {
            dump("      Code {$cf->code}: {$cf->label} (-{$cf->penalty})");
        }
    }

    expect($result->thinkingLog)->toContain('Slot jumping');
    expect($result->options)->toBeArray();
});
