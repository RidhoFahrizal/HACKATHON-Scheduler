<?php

use App\Models\AcademicCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

test('academic calendar calculates current week correctly', function () {
    $currentWeek = AcademicCalendar::getCurrentWeek();

    dump('=== ACADEMIC CURRENT WEEK ===');
    dump("Current week: {$currentWeek}");
    dump("Start date: 2026-02-09");
    dump("Today: " . now()->format('Y-m-d'));

    expect($currentWeek)->toBeGreaterThanOrEqual(1);
    expect($currentWeek)->toBeLessThanOrEqual(16);
});

test('academic calendar calculates week from date correctly', function () {
    $testCases = [
        ['date' => '2026-02-09', 'expectedWeek' => 1],
        ['date' => '2026-02-16', 'expectedWeek' => 2],
        ['date' => '2026-03-02', 'expectedWeek' => 4],
        ['date' => '2026-03-16', 'expectedWeek' => 6],
        ['date' => '2026-05-25', 'expectedWeek' => 16],
    ];

    dump('=== WEEK FROM DATE CALCULATION ===');

    foreach ($testCases as $case) {
        $week = AcademicCalendar::getWeekFromDate($case['date']);
        dump("Date: {$case['date']} -> Week: {$week} (expected: {$case['expectedWeek']})");
        expect($week)->toBe($case['expectedWeek']);
    }
});

test('academic calendar returns remaining weeks correctly', function () {
    $remaining = AcademicCalendar::getRemainingWeeks(5);

    dump('=== REMAINING WEEKS FROM WEEK 5 ===');
    dump("Remaining weeks: " . json_encode($remaining));

    expect($remaining)->toBe([5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]);
});

test('academic calendar handles date before semester start', function () {
    $week = AcademicCalendar::getWeekFromDate('2026-01-15');

    dump('=== DATE BEFORE SEMESTER START ===');
    dump("Date: 2026-01-15 -> Week: {$week} (should be 1)");

    expect($week)->toBe(1);
});

test('academic calendar caps week at total weeks', function () {
    $week = AcademicCalendar::getWeekFromDate('2026-12-31');

    dump('=== DATE AFTER SEMESTER END ===');
    dump("Date: 2026-12-31 -> Week: {$week} (should be capped at 16)");

    expect($week)->toBe(16);
});
