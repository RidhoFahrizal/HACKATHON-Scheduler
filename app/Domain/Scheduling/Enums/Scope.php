<?php

namespace App\Domain\Scheduling\Enums;

enum Scope: string
{
    case ONCE = 'once';
    case ONWARDS = 'onwards';

    public static function getCurrentWeek(): int
    {
        $startOfYear = mktime(0, 0, 0, 1, 1, (int) date('Y'));
        $now = time();
        return (int) ceil(($now - $startOfYear) / (7 * 24 * 60 * 60));
    }

    public static function getWeeksFromDate(string $date): int
    {
        $startOfYear = mktime(0, 0, 0, 1, 1, (int) date('Y', strtotime($date)));
        $target = strtotime($date);
        return (int) ceil(($target - $startOfYear) / (7 * 24 * 60 * 60));
    }

    public static function getRemainingWeeks(int $fromWeek, int $totalWeeks = 16): array
    {
        if ($fromWeek > $totalWeeks) {
            return [$fromWeek];
        }
        return range($fromWeek, $totalWeeks);
    }
}
