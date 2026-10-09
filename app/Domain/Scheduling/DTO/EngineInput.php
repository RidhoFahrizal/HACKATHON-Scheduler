<?php

namespace App\Domain\Scheduling\DTO;

class EngineInput
{
    public function __construct(
        public readonly ScheduleDto $targetSchedule,
        public readonly SubjectDto $subject,
        public readonly array $lecturerSchedules,
        public readonly array $studentSchedules,
        public readonly array $enrolledStudents,
        public readonly int $totalStudents,
        public readonly int $requiredSlots,
        public readonly array $rooms,
    ) {}

    public function toArray(): array
    {
        return [
            'schedule_id' => $this->targetSchedule->id,
            'subject_name' => $this->subject->name,
            'credits' => $this->subject->credits,
            'lecturer_id' => $this->targetSchedule->lecturerId,
            'total_students' => $this->totalStudents,
            'required_slots' => $this->requiredSlots,
            'lecturer_schedules_count' => count($this->lecturerSchedules),
            'student_schedules_count' => count($this->studentSchedules),
            'available_rooms' => count($this->rooms),
        ];
    }
}
