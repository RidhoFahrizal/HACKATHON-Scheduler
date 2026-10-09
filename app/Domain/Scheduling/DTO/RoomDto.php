<?php

namespace App\Domain\Scheduling\DTO;

use App\Models\Room as RoomModel;

class RoomDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $capacity,
    ) {}

    public static function fromModel(RoomModel $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            capacity: $model->capacity,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            capacity: $data['capacity'],
        );
    }
}
