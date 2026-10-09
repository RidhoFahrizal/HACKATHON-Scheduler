<?php

namespace App\Domain\Scheduling\DTO;

use App\Domain\Scheduling\Enums\DayOfWeek;

class ScheduleOption
{
    public function __construct(
        public readonly DayOfWeek $day,
        public readonly int $startSlot,
        public readonly int $endSlot,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly int $roomId,
        public readonly string $roomName,
        public readonly int $score,
        public readonly array $codeFactors = [],
    ) {}

    public function toArray(): array
    {
        return [
            'day' => $this->day->label(),
            'start_slot' => $this->startSlot,
            'end_slot' => $this->endSlot,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'room_id' => $this->roomId,
            'room_name' => $this->roomName,
            'score' => $this->score,
            'code_factors' => array_map(fn(CodeFactor $cf) => $cf->toArray(), $this->codeFactors),
        ];
    }
}
