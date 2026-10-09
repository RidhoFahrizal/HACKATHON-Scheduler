<?php

namespace Tests\Feature\Traits;

use App\Domain\Scheduling\DTO\RoomDto;
use App\Domain\Scheduling\DTO\ScheduleDto;
use App\Domain\Scheduling\DTO\StudentSubjectDto;
use App\Domain\Scheduling\DTO\SubjectDto;

trait HasScheduleTestData
{
    protected function getRooms(int $count = 12, int $baseCapacity = 30): array
    {
        $rooms = [];
        $names = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

        for ($i = 0; $i < min($count, 12); $i++) {
            $rooms[] = new RoomDto(
                id: (string) ($i + 1),
                name: 'Ruang ' . $names[$i],
                capacity: $baseCapacity,
            );
        }

        return $rooms;
    }

    protected function getSubjects(): array
    {
        return [
            new SubjectDto(id: '1', name: 'Matematika', credits: 2, lecturerId: '1'),
            new SubjectDto(id: '2', name: 'Fisika', credits: 2, lecturerId: '5'),
            new SubjectDto(id: '3', name: 'Kimia', credits: 3, lecturerId: '3'),
            new SubjectDto(id: '4', name: 'Biologi', credits: 2, lecturerId: '4'),
            new SubjectDto(id: '5', name: 'Sejarah', credits: 2, lecturerId: '2'),
        ];
    }

    protected function getStudents(int $count = 10, int $startId = 101): array
    {
        $students = [];
        for ($i = 0; $i < $count; $i++) {
            $students[] = ['id' => (string) ($startId + $i), 'name' => 'Student ' . chr(65 + $i)];
        }
        return $students;
    }

    protected function getStudentSubjects(string $subjectId, array $students): array
    {
        $result = [];
        foreach ($students as $i => $student) {
            $result[] = new StudentSubjectDto(
                id: (string) ($i + 1),
                studentId: $student['id'],
                subjectId: $subjectId,
            );
        }
        return $result;
    }

    protected function getBaseSchedules(): array
    {
        return [
            new ScheduleDto(
                id: '1',
                day: 0,
                startSlot: 0,
                endSlot: 1,
                subjectId: '1',
                lecturerId: '1',
                roomId: '1',
            ),
            new ScheduleDto(
                id: '2',
                day: 2,
                startSlot: 4,
                endSlot: 5,
                subjectId: '2',
                lecturerId: '5',
                roomId: '2',
            ),
            new ScheduleDto(
                id: '3',
                day: 3,
                startSlot: 5,
                endSlot: 7,
                subjectId: '3',
                lecturerId: '3',
                roomId: '3',
            ),
        ];
    }
}
