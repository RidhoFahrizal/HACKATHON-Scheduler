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

test('engine ONWARDS mode verifies pattern across all remaining weeks', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONWARDS,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(3, 40);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(4);
    $studentSubjects = $this->getStudentSubjects('1', $students);
    $schedules = $this->getBaseSchedules();

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    dump('=== ONWARDS MODE: VERIFY PATTERN ACROSS ALL REMAINING WEEKS ===');
    dump('Scope: ONWARDS');
    dump('Target week: ' . $request->targetWeek);
    dump('Weeks to evaluate: ' . json_encode($request->weeksToEvaluate));
    dump('Success: ' . ($result->success ? 'true' : 'false'));
    dump('Total options: ' . count($result->options));
    dump('Thinking Log:');
    dump($result->thinkingLog);
    dump('Top 3 Options:');
    foreach (array_slice($result->options, 0, 3) as $i => $option) {
        dump("  #" . ($i + 1) . " {$option->day->label()} {$option->startTime}-{$option->endTime} | {$option->roomName} | Score: {$option->score}");
        foreach ($option->codeFactors as $cf) {
            dump("      Code {$cf->code}: {$cf->label} (-{$cf->penalty})");
        }
    }

    expect($result->thinkingLog)->toContain('scope: onwards');
    expect($result->thinkingLog)->toContain('Verifikasi pola mingguan (ONWARDS)');
    expect($result->thinkingLog)->toContain('weeks_checked: ' . count($request->weeksToEvaluate));
    expect($result->success)->toBeBool();
    expect($result->options)->toBeArray();
});

test('engine ONCE mode does not verify across weeks', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(3, 40);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(4);
    $studentSubjects = $this->getStudentSubjects('1', $students);
    $schedules = $this->getBaseSchedules();

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    dump('=== ONCE MODE: SINGLE WEEK ONLY ===');
    dump('Scope: ONCE');
    dump('Target week: ' . $request->targetWeek);
    dump('Weeks to evaluate: ' . json_encode($request->weeksToEvaluate));
    dump('Thinking Log:');
    dump($result->thinkingLog);

    expect($result->thinkingLog)->toContain('scope: once');
    expect($result->thinkingLog)->not->toContain('Verifikasi pola mingguan (ONWARDS)');
});
