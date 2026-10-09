<?php

use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\RoomDto;
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

    $this->engine = new SchedulingEngine;
});

test('engine correctly suggests alternative room when original room is booked', function () {
    $request = new RescheduleRequest(
        scheduleId: '1',
        scope: Scope::ONCE,
        target: '2026-03-16',
    );

    $rooms = [
        new RoomDto(id: '1', name: 'Ruang A', capacity: 40),
        new RoomDto(id: '2', name: 'Ruang B', capacity: 40),
    ];

    $subjects = [
        new SubjectDto(id: '1', name: 'Matematika', credits: 2, lecturerId: '5'),
        new SubjectDto(id: '99', name: 'Mata Kuliah Lain', credits: 2, lecturerId: '99'),
    ];

    $students = $this->getStudents(10);
    $studentSubjects = $this->getStudentSubjects('1', $students);

    $schedules = [
        // Jadwal target (berada di Ruang 1)
        new ScheduleDto(
            id: '1',
            day: 0,
            startSlot: 0,
            endSlot: 1,
            subjectId: '1',
            lecturerId: '5',
            roomId: '1',
        ),
        // BLOCKER: Ditaruh di Ruang A (Ruang 1) pada slot 2 sampai 3
        new ScheduleDto(
            id: '2',
            day: 0,
            startSlot: 2,
            endSlot: 3,
            subjectId: '99',
            lecturerId: '99',
            roomId: '1', // <- Skenario diubah ke Ruang 1
        ),
    ];

    // Block semua hari Selasa(1) sampai Minggu(6) untuk fokus ke hari Senin
    $blockerId = 3;
    for ($day = 1; $day <= 6; $day++) {
        for ($slot = 0; $slot <= 14; $slot++) {
            $schedules[] = new ScheduleDto(id: 'blocker_'.$blockerId++, day: $day, startSlot: $slot, endSlot: $slot, subjectId: '99', lecturerId: '99', roomId: '1');
            $schedules[] = new ScheduleDto(id: 'blocker_'.$blockerId++, day: $day, startSlot: $slot, endSlot: $slot, subjectId: '99', lecturerId: '99', roomId: '2');
        }
    }

    // Block Senin (day 0) dari slot 4 sampai 14 biar opsi sisa dikit
    for ($slot = 4; $slot <= 14; $slot++) {
        $schedules[] = new ScheduleDto(id: 'blocker_'.$blockerId++, day: 0, startSlot: $slot, endSlot: $slot, subjectId: '99', lecturerId: '99', roomId: '1');
        $schedules[] = new ScheduleDto(id: 'blocker_'.$blockerId++, day: 0, startSlot: $slot, endSlot: $slot, subjectId: '99', lecturerId: '99', roomId: '2');
    }

    $buildResult = $this->buildEngineInput('1', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    $unsafeOptions = [];
    foreach ($result->options as $option) {
        foreach ($buildResult['allSchedules'] as $schedule) {
            if (
                $schedule->id !== $request->scheduleId
                && $schedule->roomId === $option->roomId
                && $schedule->day === $option->day->value
                && $option->startSlot <= $schedule->endSlot
                && $schedule->startSlot <= $option->endSlot
            ) {
                $unsafeOptions[] = $option;
            }
        }
    }

    $roomASuggestedForSlot2 = false;
    $roomBSuggestedForSlot2 = false;

    foreach ($result->options as $option) {
        if ($option->day->value === 0 && $option->startSlot === 2) {
            if ($option->roomId === '1') {
                $roomASuggestedForSlot2 = true;
            }
            if ($option->roomId === '2') {
                $roomBSuggestedForSlot2 = true;
            }
        }
    }

    // Ekspektasi Akhir:
    // Ruang A (1) TIDAK BOLEH disarankan karena dipakai kelas lain.
    // Ruang B (2) HARUS disarankan karena dia kosong.
    expect($roomASuggestedForSlot2)->toBeFalse();
    expect($roomBSuggestedForSlot2)->toBeTrue()
        ->and($unsafeOptions)->toBeEmpty();
});

test('engine returns no option when every suitable room is occupied at the only free slot', function () {
    $request = new RescheduleRequest('target', Scope::ONCE, '2026-03-16');
    $rooms = [new RoomDto('room-a', 'Ruang A', 40)];
    $subjects = [
        new SubjectDto('target-subject', 'Target', 2, 'target-lecturer'),
        new SubjectDto('blocker-subject', 'Blocker', 2, 'target-lecturer'),
        new SubjectDto('occupier-subject', 'Occupier', 2, 'other-lecturer'),
    ];
    $students = $this->getStudents(1);
    $studentSubjects = $this->getStudentSubjects('target-subject', $students);
    $schedules = [
        new ScheduleDto('target', 0, 0, 1, 'target-subject', 'target-lecturer', 'room-a'),
        new ScheduleDto('room-occupier', 0, 8, 9, 'occupier-subject', 'other-lecturer', 'room-a'),
    ];

    $blockerId = 1;
    foreach (range(0, 6) as $day) {
        $ranges = $day === 0 ? [[0, 7], [10, 14]] : [[0, 14]];
        foreach ($ranges as [$startSlot, $endSlot]) {
            $schedules[] = new ScheduleDto(
                'lecturer-blocker-'.$blockerId++,
                $day,
                $startSlot,
                $endSlot,
                'blocker-subject',
                'target-lecturer',
                'unused-room',
            );
        }
    }

    $buildResult = $this->buildEngineInput('target', $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    expect($result->options)->toBeEmpty()
        ->and($result->success)->toBeFalse();
});

test('engine excludes schedules from another semester when building constraints', function () {
    $rooms = [new RoomDto(id: 'room-a', name: 'Ruang A', capacity: 40)];
    $subjects = [
        new SubjectDto(id: 'subject-target', name: 'Target', credits: 2, lecturerId: 'lecturer-a'),
        new SubjectDto(id: 'subject-other', name: 'Other', credits: 2, lecturerId: 'lecturer-a'),
    ];
    $targetSchedule = new ScheduleDto(
        id: 'schedule-target',
        day: 0,
        startSlot: 0,
        endSlot: 1,
        subjectId: 'subject-target',
        lecturerId: 'lecturer-a',
        roomId: 'room-a',
        semesterType: 'ganjil',
    );
    $otherSemesterSchedule = new ScheduleDto(
        id: 'schedule-other',
        day: 0,
        startSlot: 2,
        endSlot: 3,
        subjectId: 'subject-other',
        lecturerId: 'lecturer-a',
        roomId: 'room-a',
        semesterType: 'genap',
    );
    $students = $this->getStudents(1);
    $studentSubjects = $this->getStudentSubjects('subject-target', $students);

    $buildResult = $this->buildEngineInput(
        'schedule-target',
        $rooms,
        [$targetSchedule, $otherSemesterSchedule],
        $subjects,
        $studentSubjects,
    );

    expect($buildResult['input']->lecturerSchedules)->toBe([])
        ->and($buildResult['allSchedules'])->toBe([$targetSchedule]);
});

test('schedule and room UUIDs survive request and option construction', function () {
    $scheduleId = '550e8400-e29b-41d4-a716-446655440000';
    $roomId = '550e8400-e29b-41d4-a716-446655440001';
    $subjectId = '550e8400-e29b-41d4-a716-446655440002';
    $request = new RescheduleRequest($scheduleId, Scope::ONCE, '2026-03-16');
    $rooms = [new RoomDto($roomId, 'Ruang UUID', 40)];
    $schedules = [new ScheduleDto($scheduleId, 0, 0, 1, $subjectId, 'lecturer-uuid', $roomId, 'ganjil')];
    $subjects = [new SubjectDto($subjectId, 'Target', 2, 'lecturer-uuid')];
    $students = $this->getStudents(1);
    $studentSubjects = $this->getStudentSubjects($subjectId, $students);
    $buildResult = $this->buildEngineInput($scheduleId, $rooms, $schedules, $subjects, $studentSubjects);
    $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

    expect($request->scheduleId)->toBe($scheduleId)
        ->and($result->options)->not->toBeEmpty()
        ->and($result->options[0]->roomId)->toBe($roomId)
        ->and($result->options[0]->toArray()['room_id'])->toBe($roomId);
});
