<?php

namespace App\Domain\Scheduling\DTO;

use App\Models\StudentSubject as StudentSubjectModel;

class StudentSubjectDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $studentId,
        public readonly string $subjectId,
    ) {}

    public static function fromModel(StudentSubjectModel $model): self
    {
        return new self(
            id: $model->id,
            studentId: $model->studentId,
            subjectId: $model->subjectId,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            studentId: $data['studentID'] ?? $data['studentId'] ?? '',
            subjectId: $data['subjectID'] ?? $data['subjectId'] ?? '',
        );
    }
}
