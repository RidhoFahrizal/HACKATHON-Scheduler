<?php

namespace App\Domain\Scheduling\ValueObjects;

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

    public static function fromIndex(int $index, int $slotDurationMinutes = 50): self
    {
        $startMinutes = 8 * 60 + ($index * $slotDurationMinutes);
        $endMinutes = $startMinutes + $slotDurationMinutes;

        $startHour = intdiv($startMinutes, 60);
        $startMin = $startMinutes % 60;
        $endHour = intdiv($endMinutes, 60);
        $endMin = $endMinutes % 60;

        return new self(
            index: $index,
            startTime: sprintf('%02d:%02d', $startHour, $startMin),
            endTime: sprintf('%02d:%02d', $endHour, $endMin),
        );
    }
}
