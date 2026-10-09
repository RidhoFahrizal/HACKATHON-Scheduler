<?php

namespace App\Domain\Scheduling\ValueObjects;

use InvalidArgumentException;

class TimeSlotGrid
{
    public static function generate(
        int $slotDurationMinutes,
        int $startHour,
        int $endHour,
        string $lunchStart,
        string $lunchEnd,
    ): array {
        $dayStart = $startHour * 60;
        $dayEnd = $endHour * 60;
        $lunchStartMinutes = self::minutesFromTime($lunchStart);
        $lunchEndMinutes = self::minutesFromTime($lunchEnd);

        if (
            $slotDurationMinutes <= 0
            || $dayStart >= $dayEnd
            || $lunchStartMinutes < $dayStart
            || $lunchStartMinutes >= $dayEnd
            || $lunchEndMinutes <= $lunchStartMinutes
            || $lunchEndMinutes > $dayEnd
            || ($lunchStartMinutes - $dayStart) % $slotDurationMinutes !== 0
        ) {
            throw new InvalidArgumentException('Scheduling time settings must align lunch with a valid slot boundary.');
        }

        $slots = [];
        $startMinutes = $dayStart;

        while ($startMinutes < $dayEnd) {
            if ($startMinutes === $lunchStartMinutes) {
                $slots[] = [
                    'slot_index' => count($slots),
                    'start_time' => $lunchStart,
                    'end_time' => $lunchEnd,
                    'is_blocked' => true,
                    'block_reason' => 'lunch_break',
                ];
                $startMinutes = $lunchEndMinutes;

                continue;
            }

            $endMinutes = $startMinutes + $slotDurationMinutes;
            if ($endMinutes > $dayEnd || ($startMinutes < $lunchStartMinutes && $endMinutes > $lunchStartMinutes)) {
                break;
            }

            $slots[] = [
                'slot_index' => count($slots),
                'start_time' => self::formatMinutes($startMinutes),
                'end_time' => self::formatMinutes($endMinutes),
                'is_blocked' => false,
                'block_reason' => null,
            ];
            $startMinutes = $endMinutes;
        }

        return $slots;
    }

    private static function minutesFromTime(string $time): int
    {
        if (! preg_match('/^(\d{2}):(\d{2})$/', $time, $matches)) {
            throw new InvalidArgumentException('Time must use HH:MM format.');
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];
        if ($hours > 23 || $minutes > 59) {
            throw new InvalidArgumentException('Time must be a valid clock time.');
        }

        return ($hours * 60) + $minutes;
    }

    private static function formatMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
