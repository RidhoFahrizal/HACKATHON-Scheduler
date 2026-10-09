<?php

namespace App\Domain\Scheduling\Enums;

use App\Models\AcademicCalendar;

enum Scope: string
{
    case ONCE = 'once';
    case ONWARDS = 'onwards';

    public static function getCurrentWeek(): int
    {
        return AcademicCalendar::getCurrentWeek();
    }

    public static function getWeekFromDate(string $date): int
    {
        return AcademicCalendar::getWeekFromDate($date);
    }

    public static function getRemainingWeeks(int $fromWeek): array
    {
        return AcademicCalendar::getRemainingWeeks($fromWeek);
    }
}
