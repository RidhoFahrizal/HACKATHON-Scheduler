<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\Engine\SchedulingEngine;
use App\Domain\Scheduling\Enums\Scope;

beforeEach(function () {
    $this->engine = new SchedulingEngine();
});

test('engine returns top 10 options with code factors', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-10-12', // bisa juga diubah kaya nerima minggu, sekarang minggu 8, dia ngisi 9
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang A', 'capacity' => 120],
        ['id' => 2, 'name' => 'Ruang B', 'capacity' => 60],
        ['id' => 3, 'name' => 'Ruang C', 'capacity' => 30],
        ['id' => 4, 'name' => 'Ruang D', 'capacity' => 120],
        ['id' => 5, 'name' => 'Ruang E', 'capacity' => 60],
        ['id' => 6, 'name' => 'Ruang F', 'capacity' => 30],
        ['id' => 7, 'name' => 'Ruang G', 'capacity' => 120],
        ['id' => 8, 'name' => 'Ruang H', 'capacity' => 60],
        ['id' => 9, 'name' => 'Ruang I', 'capacity' => 30],
        ['id' => 10, 'name' => 'Ruang J', 'capacity' => 120],
        ['id' => 11, 'name' => 'Ruang K', 'capacity' => 60],
        ['id' => 12, 'name' => 'Ruang L', 'capacity' => 30],

    ];

    $subjects = [
        ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
        ['id' => 2, 'name' => 'Fisika', 'credits' => 2],
        ['id' => 3, 'name' => 'Kimia', 'credits' => 2],
        ['id' => 4, 'name' => 'Biologi', 'credits' => 2],
        ['id' => 5, 'name' => 'Sejarah', 'credits' => 2],
        ['id' => 6, 'name' => 'Geografi', 'credits' => 2],
        ['id' => 7, 'name' => 'Seni', 'credits' => 2],
        ['id' => 8, 'name' => 'Olahraga', 'credits' => 2],
        ['id' => 9, 'name' => 'Pancasila', 'credits' => 2],
    ];

    $students = [
        ['id' => 101, 'name' => 'Student A'],
        ['id' => 102, 'name' => 'Student B'],
        ['id' => 103, 'name' => 'Student C'],
        ['id' => 104, 'name' => 'Student D'],
        ['id' => 105, 'name' => 'Student E'],
        ['id' => 106, 'name' => 'Student F'],
        ['id' => 107, 'name' => 'Student G'],
        ['id' => 108, 'name' => 'Student H'],
        ['id' => 109, 'name' => 'Student I'],
        ['id' => 110, 'name' => 'Student J'],
    ];

    $lecturers = [
        ['id' => 1, 'name' => 'Lecturer X'],
        ['id' => 2, 'name' => 'Lecturer Y'],
        ['id' => 3, 'name' => 'Lecturer Z'],
        ['id' => 4, 'name' => 'Lecturer A'],
        ['id' => 5, 'name' => 'Lecturer B'],
    ];

    $studentSubjects = [
        [
            'studentID' => 101,
            'subjectID' => 1,
        ],
        [
            'studentID' => 101,
            'subjectID' => 2,
        ],
        [
            'studentID' => 101,
            'subjectID' => 3,
        ],
        [
            'studentID' => 102,
            'subjectID' => 2,
        ],
        [
            'studentID' => 102,
            'subjectID' => 1,
        ],
        [
            'studentID' => 102,
            'subjectID' => 3,
        ],
        [
            'studentID' => 103,
            'subjectID' => 1,
        ],
        [
            'studentID' => 103,
            'subjectID' => 2,
        ],
        [
            'studentID' => 103,
            'subjectID' => 3,
        ],
        [
            'studentID' => 104,
            'subjectID' => 1,
        ],
        [
            'studentID' => 104,
            'subjectID' => 2,
        ],
        [
            'studentID' => 104,
            'subjectID' => 3,
        ],
    ];

    $schedules = [
        [
            'id' => 1,
            'day' => 0,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 1,
            'roomID' => 1,
            'bookingID' => 0,
        ],
        [
            'id' => 2,
            'day' => 2,
            'startSlot' => 4,
            'endSlot' => 5,
            'subjectID' => 2,
            'lecturerID' => 5,
            'roomID' => 2,
            'bookingID' => 0,
        ],
        [
            'id' => 3,
            'day' => 3, // Thursday
            'startSlot' => 5, // 11:20 AM
            'endSlot' => 7, // walaupun 2 sks, melewati istirahat(6)
            'subjectID' => 3,
            'lecturerID' => 3,
            'roomID' => 3,
            'bookingID' => 0,
        ],
    ];

    $lecturerSchedules = array_filter($schedules, fn($s) => $s['lecturerID'] === 5);

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

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

test('engine handles friday prayer time correctly', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-10-16',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang A', 'capacity' => 40],
    ];

    $subjects = [
        ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
    ];

    $students = [
        ['id' => 101, 'name' => 'Student A'],
    ];

    $studentSubjects = [
        ['studentID' => 101, 'subjectID' => 1],
    ];

    $schedules = [
        [
            'id' => 1,
            'day' => 4,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 1,
            'roomID' => 1,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    foreach ($result->options as $option) {
        if ($option->day->isFriday()) {
            expect($option->endTime)->toBeLessThanOrEqual('11:20');
        }
    }
});

test('engine filters rooms by capacity', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-10-12',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang Kecil', 'capacity' => 20],
        ['id' => 2, 'name' => 'Ruang Besar', 'capacity' => 40],
    ];

    $subjects = [
        ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
    ];

    $students = [];
    for ($i = 101; $i <= 130; $i++) {
        $students[] = ['id' => $i, 'name' => "Student $i"];
    }

    $studentSubjects = [];
    foreach ($students as $student) {
        $studentSubjects[] = ['studentID' => $student['id'], 'subjectID' => 1];
    }

    $schedules = [
        [
            'id' => 1,
            'day' => 0,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 5,
            'roomID' => 1,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    foreach ($result->options as $option) {
        expect($option->roomName)->toBe('Ruang Besar');
    }
});

test('engine applies early morning penalty', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-10-12',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang A', 'capacity' => 40],
    ];

    $subjects = [
        ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
    ];

    $students = [];
    for ($i = 101; $i <= 110; $i++) {
        $students[] = ['id' => $i, 'name' => "Student $i"];
    }

    $studentSubjects = [];
    foreach ($students as $student) {
        $studentSubjects[] = ['studentID' => $student['id'], 'subjectID' => 1];
    }

    $schedules = [
        [
            'id' => 1,
            'day' => 0,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 5,
            'roomID' => 1,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    $earlyOptions = array_filter($result->options, fn($opt) => $opt->startSlot === 0);

    foreach ($earlyOptions as $option) {
        $hasEarlyPenalty = false;
        foreach ($option->codeFactors as $factor) {
            if ($factor->code === 4) {
                $hasEarlyPenalty = true;
                expect($factor->label)->toBe('Jam terlalu pagi');
            }
        }
        expect($hasEarlyPenalty)->toBeTrue();
    }
});

test('engine returns false when no good slots available', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-10-12',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang Kecil', 'capacity' => 10],
    ];

    $subjects = [
        ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
    ];

    $students = [];
    for ($i = 101; $i <= 130; $i++) {
        $students[] = ['id' => $i, 'name' => "Student $i"];
    }

    $studentSubjects = [];
    foreach ($students as $student) {
        $studentSubjects[] = ['studentID' => $student['id'], 'subjectID' => 1];
    }

    $schedules = [
        [
            'id' => 1,
            'day' => 0,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 5,
            'roomID' => 1,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    expect($result->success)->toBeFalse();
    expect($result->options)->toBeEmpty();
});

test('thinking log contains all steps', function () {
    $request = new RescheduleRequest(
        scheduleId: 1,
        scope: Scope::ONCE,
        target: '2026-10-12',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang A', 'capacity' => 40],
    ];

    $subjects = [
        ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
    ];

    $students = [];
    for ($i = 101; $i <= 110; $i++) {
        $students[] = ['id' => $i, 'name' => "Student $i"];
    }

    $studentSubjects = [];
    foreach ($students as $student) {
        $studentSubjects[] = ['studentID' => $student['id'], 'subjectID' => 1];
    }

    $schedules = [
        [
            'id' => 1,
            'day' => 0,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 5,
            'roomID' => 1,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    expect($result->thinkingLog)->toContain('[STEP 1: Ambil bahan]');
    expect($result->thinkingLog)->toContain('[STEP 2: Bangun bitmask]');
    expect($result->thinkingLog)->toContain('[STEP 3: Cari jendela slot]');
    expect($result->thinkingLog)->toContain('[STEP 4: Filter ruang]');
    expect($result->thinkingLog)->toContain('[STEP 5: Scoring]');
});

test('engine handles slot jumping over lunch break', function () {
    $request = new RescheduleRequest(
        scheduleId: 3,
        scope: Scope::ONCE,
        target: '2026-10-12',
    );

    $rooms = [
        ['id' => 1, 'name' => 'Ruang A', 'capacity' => 40],
        ['id' => 2, 'name' => 'Ruang B', 'capacity' => 40],
        ['id' => 3, 'name' => 'Ruang C', 'capacity' => 40],
    ];

    $subjects = [
        ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
        ['id' => 2, 'name' => 'Fisika', 'credits' => 2],
        ['id' => 3, 'name' => 'Kimia', 'credits' => 3],
    ];

    $students = [
        ['id' => 101, 'name' => 'Student A'],
        ['id' => 102, 'name' => 'Student B'],
    ];

    $studentSubjects = [
        ['studentID' => 101, 'subjectID' => 1],
        ['studentID' => 101, 'subjectID' => 2],
        ['studentID' => 101, 'subjectID' => 3],
        ['studentID' => 102, 'subjectID' => 1],
        ['studentID' => 102, 'subjectID' => 2],
        ['studentID' => 102, 'subjectID' => 3],
    ];

    $schedules = [
        [
            'id' => 1,
            'day' => 0,
            'startSlot' => 0,
            'endSlot' => 1,
            'subjectID' => 1,
            'lecturerID' => 1,
            'roomID' => 1,
            'bookingID' => 0,
        ],
        [
            'id' => 2,
            'day' => 2,
            'startSlot' => 4,
            'endSlot' => 5,
            'subjectID' => 2,
            'lecturerID' => 2,
            'roomID' => 2,
            'bookingID' => 0,
        ],
        [
            'id' => 3,
            'day' => 3,
            'startSlot' => 5,
            'endSlot' => 7,
            'subjectID' => 3,
            'lecturerID' => 3,
            'roomID' => 3,
            'bookingID' => 0,
        ],
    ];

    $result = $this->engine->evaluate($request, $rooms, $schedules, $subjects, $students, $studentSubjects);

    expect($result->thinkingLog)->toContain('Slot jumping');

    $jumpingOptions = array_filter($result->options, fn($opt) => true);
    expect($result->options)->toBeArray();
});
