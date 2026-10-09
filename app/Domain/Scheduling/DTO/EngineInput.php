<?php

namespace App\Domain\Scheduling\DTO;

class EngineInput
{
    public function __construct(
        public readonly mixed $targetSchedule,
        public readonly mixed $subject,
        public readonly array $lecturerSchedules,
        public readonly array $studentSchedules,
        public readonly array $enrolledStudentIds,
        public readonly int $totalStudents,
        public readonly int $requiredSlots,
        public readonly array $rooms,
    ) {}

    public function toArray(): array
    {
        $scheduleId = is_array($this->targetSchedule) ? ($this->targetSchedule['id'] ?? null) : ($this->targetSchedule->id ?? null);
        $subjectName = is_array($this->subject) ? ($this->subject['name'] ?? null) : ($this->subject->name ?? null);
        $credits = is_array($this->subject) ? ($this->subject['credits'] ?? null) : ($this->subject->credits ?? null);
        $lecturerId = is_array($this->targetSchedule) ? ($this->targetSchedule['lecturerID'] ?? null) : ($this->targetSchedule->lecturerId ?? null);

        return [
            'schedule_id' => $scheduleId,
            'subject_name' => $subjectName,
            'credits' => $credits,
            'lecturer_id' => $lecturerId,
            'total_students' => $this->totalStudents,
            'required_slots' => $this->requiredSlots,
            'lecturer_schedules_count' => count($this->lecturerSchedules),
            'student_schedules_count' => count($this->studentSchedules),
            'available_rooms' => count($this->rooms),
        ];
    }
}
