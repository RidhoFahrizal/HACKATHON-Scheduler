<?php

namespace Tests\Feature\Traits;

trait HasScheduleTestData
{
    protected function getRooms(int $count = 12, int $baseCapacity = 30): array
    {
        $rooms = [];
        $names = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

        for ($i = 0; $i < min($count, 12); $i++) {
            $rooms[] = [
                'id' => $i + 1,
                'name' => 'Ruang ' . $names[$i],
                'capacity' => $baseCapacity,
            ];
        }

        return $rooms;
    }

    protected function getSubjects(): array
    {
        return [
            ['id' => 1, 'name' => 'Matematika', 'credits' => 2],
            ['id' => 2, 'name' => 'Fisika', 'credits' => 2],
            ['id' => 3, 'name' => 'Kimia', 'credits' => 3],
            ['id' => 4, 'name' => 'Biologi', 'credits' => 2],
            ['id' => 5, 'name' => 'Sejarah', 'credits' => 2],
        ];
    }

    protected function getStudents(int $count = 10, int $startId = 101): array
    {
        $students = [];
        for ($i = 0; $i < $count; $i++) {
            $students[] = ['id' => $startId + $i, 'name' => 'Student ' . chr(65 + $i)];
        }
        return $students;
    }

    protected function getStudentSubjects(int $subjectId, array $students): array
    {
        return array_map(fn($s) => ['studentID' => $s['id'], 'subjectID' => $subjectId], $students);
    }

    protected function getBaseSchedules(): array
    {
        return [
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
                'day' => 3,
                'startSlot' => 5,
                'endSlot' => 7,
                'subjectID' => 3,
                'lecturerID' => 3,
                'roomID' => 3,
                'bookingID' => 0,
            ],
        ];
    }
}
