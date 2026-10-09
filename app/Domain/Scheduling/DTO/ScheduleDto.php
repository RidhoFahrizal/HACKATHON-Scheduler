<?php

namespace App\Domain\Scheduling\DTO;

use App\Models\Schedule as ScheduleModel;

class ScheduleDto
{
    public function __construct(
        public readonly string $id,
        public readonly int $day,
        public readonly int $startSlot,
        public readonly int $endSlot,
        public readonly string $subjectId,
        public readonly string $lecturerId,
        public readonly string $roomId,
        public readonly string $semesterType = '',
    ) {}

    public static function fromModel(ScheduleModel $model): self
    {
        return new self(
            id: $model->id,
            day: $model->day,
            startSlot: $model->startSlot,
            endSlot: $model->endSlot,
            subjectId: $model->subjectId,
            lecturerId: $model->lecturerId,
            roomId: $model->roomId,
            semesterType: $model->semesterType,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            day: $data['day'],
            startSlot: $data['startSlot'],
            endSlot: $data['endSlot'],
            subjectId: $data['subjectID'] ?? $data['subjectId'] ?? '',
            lecturerId: $data['lecturerID'] ?? $data['lecturerId'] ?? '',
            roomId: $data['roomID'] ?? $data['roomId'] ?? '',
            semesterType: $data['semesterType'] ?? '',
        );
    }
}
