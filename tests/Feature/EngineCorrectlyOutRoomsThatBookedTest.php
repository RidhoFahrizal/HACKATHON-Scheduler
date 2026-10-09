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

    $roomASuggestedForSlot2 = false;
    $roomBSuggestedForSlot2 = false;

    foreach ($result->options as $option) {
        if ($option->day->value === 0 && $option->startSlot === 2) {
            if ($option->roomId === 1) {
                $roomASuggestedForSlot2 = true;
            }
            if ($option->roomId === 2) {
                $roomBSuggestedForSlot2 = true;
            }
        }
    }

    // Ekspektasi Akhir:
    // Ruang A (1) TIDAK BOLEH disarankan karena dipakai kelas lain.
    // Ruang B (2) HARUS disarankan karena dia kosong.
    expect($roomASuggestedForSlot2)->toBeFalse();
    expect($roomBSuggestedForSlot2)->toBeTrue();
});
