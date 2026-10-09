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

test('engine streams thinking steps via generator', function () {
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
    
    $steps = [];
    foreach ($this->engine->evaluateStream($request, $buildResult['input'], $buildResult['allSchedules']) as $step) {
        $steps[] = $step;
    }

    dump('=== ENGINE STREAMING TEST ===');
    dump('Total steps streamed: ' . count($steps));
    
    foreach ($steps as $i => $step) {
        dump("Step " . ($i + 1) . ": {$step->title} (code: {$step->thinkingCode})");
        if (!empty($step->details)) {
            foreach ($step->details as $key => $value) {
                $displayValue = is_array($value) ? json_encode($value) : $value;
                dump("  - {$key}: {$displayValue}");
            }
        }
    }

    expect($steps)->toBeArray();
    expect(count($steps))->toBeGreaterThan(5);
    
    // Verify step structure
    $firstStep = $steps[0];
    expect($firstStep->stepNumber)->toBe(1);
    expect($firstStep->title)->toBe('Ambil bahan');
    expect($firstStep->thinkingCode)->toBe(0);
    
    // Verify last step is result
    $lastStep = end($steps);
    expect($lastStep->stepNumber)->toBe('result');
    expect($lastStep->title)->toBe('Hasil akhir');
    expect($lastStep->details)->toHaveKey('success');
    expect($lastStep->details)->toHaveKey('options_count');
});

test('engine streaming yields steps in correct order', function () {
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
    
    $stepTitles = [];
    foreach ($this->engine->evaluateStream($request, $buildResult['input'], $buildResult['allSchedules']) as $step) {
        $stepTitles[] = $step->title;
    }

    dump('=== STEP ORDER TEST (ONWARDS) ===');
    dump('Steps: ' . implode(' → ', $stepTitles));

    expect($stepTitles)->toContain('Ambil bahan');
    expect($stepTitles)->toContain('Bangun bitmask');
    expect($stepTitles)->toContain('Cari jendela slot');
    expect($stepTitles)->toContain('Slot jumping (lompati istirahat)');
    expect($stepTitles)->toContain('Filter ruang');
    expect($stepTitles)->toContain('Verifikasi pola mingguan (ONWARDS)');
    expect($stepTitles)->toContain('Scoring');
    expect($stepTitles)->toContain('Hasil akhir');
});
