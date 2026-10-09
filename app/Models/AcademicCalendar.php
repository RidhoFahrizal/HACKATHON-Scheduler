<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    protected $fillable = [
        'name',
        'year',
        'semester',
        'start_date',
        'end_date',
        'total_weeks',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_weeks' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getCurrentWeek(): int
    {
        $calendar = static::active()->first();

        if (!$calendar) {
            return 1;
        }

        $today = now()->startOfDay();
        $startDate = $calendar->start_date->startOfDay();

        if ($today->lt($startDate)) {
            return 1;
        }

        $daysDiff = $startDate->diffInDays($today);
        $currentWeek = (int) floor($daysDiff / 7) + 1;

        return min($currentWeek, $calendar->total_weeks);
    }

    public static function getWeekFromDate(string $date): int
    {
        $calendar = static::active()->first();

        if (!$calendar) {
            return 1;
        }

        $targetDate = \Carbon\Carbon::parse($date)->startOfDay();
        $startDate = $calendar->start_date->startOfDay();

        if ($targetDate->lt($startDate)) {
            return 1;
        }

        $daysDiff = $startDate->diffInDays($targetDate);
        $week = (int) floor($daysDiff / 7) + 1;

        return min($week, $calendar->total_weeks);
    }

    public static function getRemainingWeeks(int $fromWeek): array
    {
        $calendar = static::active()->first();
        $totalWeeks = $calendar ? $calendar->total_weeks : 16;

        if ($fromWeek > $totalWeeks) {
            return [$fromWeek];
        }

        return range($fromWeek, $totalWeeks);
    }
}
