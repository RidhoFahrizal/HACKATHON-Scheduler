<?php

namespace App\Domain\Scheduling\Support;

use App\Domain\Scheduling\ValueObjects\TimeSlotGrid;
use App\Models\Setting;

final class SlotMapper
{
    /** @var list<array{slot_index: int, start_time: string, end_time: string, is_blocked: bool, block_reason: string|null}>|null */
    private ?array $grid = null;

    /**
     * @return list<array{slot_index: int, start_time: string, end_time: string, is_blocked: bool, block_reason: string|null}>
     */
    public function grid(): array
    {
        return $this->grid ??= TimeSlotGrid::generate(
            (int) Setting::get('slot_duration_minutes', 50),
            (int) Setting::get('min_start_hour', 7),
            (int) Setting::get('max_end_hour', 20),
            (string) Setting::get('lunch_break_start', '12:00'),
            (string) Setting::get('lunch_break_end', '13:00'),
        );
    }

    /**
     * Free-form clock times (for example from a form) rarely sit on the slot grid, so unaligned input yields null
     * instead of a rounded guess that would silently move a class.
     *
     * @return array{0: int, 1: int}|null inclusive [startSlot, endSlot]
     */
    public function rangeFor(string $startTime, string $endTime): ?array
    {
        $start = $this->normalize($startTime);
        $end = $this->normalize($endTime);
        $startSlot = null;
        $endSlot = null;

        foreach ($this->grid() as $slot) {
            if ($slot['is_blocked']) {
                continue;
            }
            if ($slot['start_time'] === $start) {
                $startSlot = $slot['slot_index'];
            }
            if ($slot['end_time'] === $end) {
                $endSlot = $slot['slot_index'];
            }
        }

        if ($startSlot === null || $endSlot === null || $endSlot < $startSlot) {
            return null;
        }

        return [$startSlot, $endSlot];
    }

    /**
     * @return array{start_time: string, end_time: string}|null
     */
    public function timesFor(int $startSlot, int $endSlot): ?array
    {
        $grid = $this->grid();

        if (! isset($grid[$startSlot], $grid[$endSlot])) {
            return null;
        }

        return [
            'start_time' => $grid[$startSlot]['start_time'],
            'end_time' => $grid[$endSlot]['end_time'],
        ];
    }

    private function normalize(string $time): string
    {
        if (preg_match('/^(\d{1,2}):(\d{2})/', trim($time), $parts) !== 1) {
            return '';
        }

        return sprintf('%02d:%02d', (int) $parts[1], (int) $parts[2]);
    }
}
