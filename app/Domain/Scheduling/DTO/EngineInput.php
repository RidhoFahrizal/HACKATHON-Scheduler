<?php

namespace App\Domain\Scheduling\DTO;

class EngineInput
{
    public function __construct(
        public readonly array $targetSchedule,
        public readonly array $subject,
        public readonly array $lecturerSchedules,
        public readonly array $studentSchedules,
        public readonly array $enrolledStudentIds,
        public readonly int $totalStudents,
        public readonly int $requiredSlots,
        public readonly array $rooms,
    ) {}

    public function toArray(): array
    {
        return [
            'schedule_id' => $this->targetSchedule['id'] ?? null,
            'subject_name' => $this->subject['name'] ?? null,
            'credits' => $this->subject['credits'] ?? null,
            'lecturer_id' => $this->targetSchedule['lecturerID'] ?? null,
            'total_students' => $this->totalStudents,
            'required_slots' => $this->requiredSlots,
            'lecturer_schedules_count' => count($this->lecturerSchedules),
            'student_schedules_count' => count($this->studentSchedules),
            'available_rooms' => count($this->rooms),
        ];
    }
}
