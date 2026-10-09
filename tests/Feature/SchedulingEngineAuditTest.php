<?php

use App\Domain\Scheduling\DTO\EngineRules;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\RoomDto;
use App\Domain\Scheduling\DTO\ScheduleDto;
use App\Domain\Scheduling\DTO\SubjectDto;
use App\Domain\Scheduling\Engine\SchedulingEngine;
use App\Domain\Scheduling\Enums\Scope;
use App\Domain\Scheduling\ValueObjects\TimeSlot;
use App\Domain\Scheduling\ValueObjects\TimeSlotGrid;
use App\Models\AcademicCalendar;
use App\Models\TimeSlot as TimeSlotModel;
use Database\Seeders\SchedulingConfigSeeder;
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
});

test('time slot grid represents lunch once and resumes at 13:00', function () {
    $slots = TimeSlotGrid::generate(50, 7, 20, '12:00', '13:00');

    expect($slots)->toHaveCount(15)
        ->and($slots[5])->toMatchArray([
            'start_time' => '11:10',
            'end_time' => '12:00',
            'is_blocked' => false,
        ])
        ->and($slots[6])->toMatchArray([
            'start_time' => '12:00',
            'end_time' => '13:00',
            'is_blocked' => true,
            'block_reason' => 'lunch_break',
        ])
        ->and($slots[7])->toMatchArray([
            'start_time' => '13:00',
            'end_time' => '13:50',
            'is_blocked' => false,
        ]);

    foreach (array_slice($slots, 0, -1) as $index => $slot) {
        expect($slot['end_time'])->toBe($slots[$index + 1]['start_time']);
    }
});

test('time slot grid rejects lunch boundaries that split a teaching slot', function () {
    expect(fn () => TimeSlotGrid::generate(40, 7, 20, '12:00', '13:00'))
        ->toThrow(InvalidArgumentException::class);
});

test('time slot value object uses the same indexed grid as the engine', function () {
    expect(TimeSlot::fromIndex(6)->startTime)->toBe('12:00')
        ->and(TimeSlot::fromIndex(6)->endTime)->toBe('13:00')
        ->and(TimeSlot::fromIndex(7)->startTime)->toBe('13:00')
        ->and(fn () => TimeSlot::fromIndex(99))->toThrow(OutOfBoundsException::class);
});

test('configuration seeder persists the same lunch-aware grid used by the engine', function () {
    $this->seed(SchedulingConfigSeeder::class);
    $slots = TimeSlotModel::query()->orderBy('slot_index')->get();

    expect($slots)->toHaveCount(15)
        ->and($slots[5]->start_time)->toBe('11:10')
        ->and($slots[6]->start_time)->toBe('12:00')
        ->and($slots[6]->end_time)->toBe('13:00')
        ->and($slots[6]->is_blocked)->toBeTrue()
        ->and($slots[7]->start_time)->toBe('13:00');
});

test('engine uses injected duration and option-count rules', function () {
    $rules = new EngineRules(slotDurationMinutes: 25, maxOptions: 1, minimumSuccessScore: 100);
    $rooms = $this->getRooms(3, 40);
    $schedules = $this->getBaseSchedules();
    $subjects = $this->getSubjects();
    $students = $this->getStudents(4);
    $studentSubjects = $this->getStudentSubjects('1', $students);
    $buildResult = SchedulingEngine::buildInput('1', $rooms, $schedules, $subjects, $studentSubjects, $rules);

    $result = (new SchedulingEngine)->evaluate(
        new RescheduleRequest('1', Scope::ONCE, '2026-03-16'),
        $buildResult['input'],
        $buildResult['allSchedules'],
    );

    expect($buildResult['input']->requiredSlots)->toBe(4)
        ->and($result->options)->toHaveCount(1)
        ->and($result->success)->toBeFalse();
});

test('engine rejects mismatched target and invalid existing schedule ranges', function () {
    $rooms = $this->getRooms(1, 40);
    $subjects = $this->getSubjects();
    $students = $this->getStudents(1);
    $studentSubjects = $this->getStudentSubjects('1', $students);
    $schedules = $this->getBaseSchedules();
    $buildResult = SchedulingEngine::buildInput('1', $rooms, $schedules, $subjects, $studentSubjects);

    expect(fn () => (new SchedulingEngine)->evaluate(
        new RescheduleRequest('2', Scope::ONCE, '2026-03-16'),
        $buildResult['input'],
        $buildResult['allSchedules'],
    ))->toThrow(InvalidArgumentException::class);

    $invalidSchedules = [new ScheduleDto('target', 0, 0, 99, 'subject', 'lecturer', 'room', 'ganjil')];
    $invalidInput = SchedulingEngine::buildInput(
        'target',
        $rooms,
        $invalidSchedules,
        [new SubjectDto('subject', 'Target', 2, 'lecturer')],
        $this->getStudentSubjects('subject', $students),
    );

    expect(fn () => (new SchedulingEngine)->evaluate(
        new RescheduleRequest('target', Scope::ONCE, '2026-03-16'),
        $invalidInput['input'],
        $invalidInput['allSchedules'],
    ))->toThrow(InvalidArgumentException::class);
});

test('capacity score strictly prefers the room with less unused capacity', function () {
    $request = new RescheduleRequest('target', Scope::ONCE, '2026-03-16');
    $rooms = [
        new RoomDto(id: 'room-30', name: 'Ruang 30', capacity: 30),
        new RoomDto(id: 'room-32', name: 'Ruang 32', capacity: 32),
        new RoomDto(id: 'room-45', name: 'Ruang 45', capacity: 45),
        new RoomDto(id: 'room-100', name: 'Ruang 100', capacity: 100),
        new RoomDto(id: 'room-101', name: 'Ruang 101', capacity: 101),
    ];
    $subjects = [
        new SubjectDto(id: 'subject', name: 'Kelas besar', credits: 2, lecturerId: 'lecturer'),
        new SubjectDto(id: 'blocker-subject', name: 'Blocker', credits: 2, lecturerId: 'lecturer'),
    ];
    $students = $this->getStudents(29);
    $studentSubjects = $this->getStudentSubjects('subject', $students);
    $schedules = [
        new ScheduleDto('target', 0, 0, 1, 'subject', 'lecturer', 'room-30', 'ganjil'),
    ];

    $blockerId = 1;
    foreach (range(0, 6) as $day) {
        $blockedRanges = $day === 0 ? [[0, 7], [10, 14]] : [[0, 14]];
        foreach ($blockedRanges as [$startSlot, $endSlot]) {
            $schedules[] = new ScheduleDto(
                'blocker-'.$blockerId++,
                $day,
                $startSlot,
                $endSlot,
                'blocker-subject',
                'lecturer',
                'unavailable-room',
                'ganjil',
            );
        }
    }

    $buildResult = SchedulingEngine::buildInput('target', $rooms, $schedules, $subjects, $studentSubjects);
    $result = (new SchedulingEngine)->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    expect($result->options)->toHaveCount(5)
        ->and(array_map(fn ($option) => $option->roomId, $result->options))
        ->toBe(['room-30', 'room-32', 'room-45', 'room-100', 'room-101'])
        ->and($result->options[0]->score)->toBeGreaterThan($result->options[1]->score)
        ->and($result->options[1]->score)->toBeGreaterThan($result->options[2]->score)
        ->and($result->options[3]->score)->toBe($result->options[4]->score);
});
