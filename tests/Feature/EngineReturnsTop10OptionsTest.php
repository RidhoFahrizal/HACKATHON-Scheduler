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

test('engine returns top 10 options with code factors', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = $this->getRooms(12, 30);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(4);
    $studentSubjects = $this->getStudentSubjects('1', $students);
    $schedules = $this->getBaseSchedules();

    $input = SchedulingEngine::buildInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $input);

    dump('=== ENGINE RETURNS TOP 10 OPTIONS ===');
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

    expect($result->success)->toBeBool();
    expect($result->thinkingLog)->toBeString();
    expect($result->options)->toBeArray();
    expect(count($result->options))->toBeLessThanOrEqual(10);

    if (count($result->options) > 0) {
        $firstOption = $result->options[0];
        expect($firstOption->score)->toBeGreaterThanOrEqual(0);
        expect($firstOption->score)->toBeLessThanOrEqual(100);
        expect($firstOption->codeFactors)->toBeArray();
    }
});
