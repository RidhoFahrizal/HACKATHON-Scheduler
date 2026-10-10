<?php

namespace App\Domain\Scheduling\DTO;

use App\Models\Subject as SubjectModel;

class SubjectDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $credits,
        public readonly string $lecturerId,
    ) {}

    public static function fromModel(SubjectModel $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            credits: $model->credits,
            lecturerId: $model->lecturerId ?? '',
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            credits: $data['credits'],
            lecturerId: $data['lecturerId'] ?? '',
        );
    }
}
