<?php

namespace App\Domain\Scheduling\DTO;

class EngineInput
{
    public readonly array $studentIds;

    public readonly array $subjectStudentMap;

    public readonly EngineRules $rules;

    public function __construct(
        public readonly ScheduleDto $targetSchedule,
        public readonly SubjectDto $subject,
        public readonly array $lecturerSchedules,
        public readonly array $studentSchedules,
        public readonly array $enrolledStudents,
        public readonly int $totalStudents,
        public readonly int $requiredSlots,
        public readonly array $rooms,
        public readonly array $allStudentSubjects = [],
        ?EngineRules $rules = null,
    ) {
        $this->rules = $rules ?? new EngineRules;
        $this->studentIds = array_values(array_unique(
            array_map(fn ($e) => $e->studentId, $enrolledStudents)
        ));

        if ($requiredSlots < 1 || $totalStudents !== count($this->studentIds)) {
            throw new \InvalidArgumentException('Engine input must have positive slot demand and an accurate unique student count.');
        }

        $this->subjectStudentMap = $this->buildSubjectStudentMap();
    }

    private function buildSubjectStudentMap(): array
    {
        $map = [];
        foreach ($this->allStudentSubjects as $ss) {
            $map[$ss->subjectId][] = $ss->studentId;
        }

        return $map;
    }

    public function getStudentsForSubject(string $subjectId): array
    {
        return $this->subjectStudentMap[$subjectId] ?? [];
    }

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
            'algorithm_rules' => $this->rules->toArray(),
        ];
    }
}
