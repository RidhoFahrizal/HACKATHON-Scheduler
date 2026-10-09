<?php

namespace App\Domain\Scheduling\ValueObjects;

use OutOfBoundsException;

class TimeSlot
{
    public function __construct(
        public readonly int $index,
        public readonly string $startTime,
        public readonly string $endTime,
    ) {}

    public function startHour(): int
    {
        return (int) explode(':', $this->startTime)[0];
    }

    public function endHour(): int
    {
        return (int) explode(':', $this->endTime)[0];
    }

    public function endMinute(): int
    {
        return (int) explode(':', $this->endTime)[1];
    }

    public static function fromIndex(
        int $index,
        int $slotDurationMinutes = 50,
        int $startHour = 7,
        int $endHour = 20,
        string $lunchStart = '12:00',
        string $lunchEnd = '13:00',
    ): self {
        $slot = TimeSlotGrid::generate($slotDurationMinutes, $startHour, $endHour, $lunchStart, $lunchEnd)[$index] ?? null;

        if ($slot === null) {
            throw new OutOfBoundsException("Slot index {$index} is outside the configured time grid.");
        }

        return new self($index, $slot['start_time'], $slot['end_time']);
    }
}
