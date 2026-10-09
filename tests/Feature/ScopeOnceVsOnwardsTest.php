<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\Enums\Scope;
use App\Models\AcademicCalendar;

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
});

test('scope ONCE evaluates single week, ONWARDS evaluates remaining weeks', function () {
    $onceRequest = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $onwardsRequest = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONWARDS,
        target: '2026-03-16',
    );

    dump('=== SCOPE ONCE vs ONWARDS ===');
    dump("ONCE  -> target week: {$onceRequest->targetWeek}, weeks to evaluate: " . json_encode($onceRequest->weeksToEvaluate));
    dump("ONWARDS -> target week: {$onwardsRequest->targetWeek}, weeks to evaluate: " . json_encode($onwardsRequest->weeksToEvaluate));

    expect($onceRequest->weeksToEvaluate)->toBe([$onceRequest->targetWeek]);
    expect(count($onwardsRequest->weeksToEvaluate))->toBeGreaterThan(1);
});
