<?php

namespace App\Domain\Scheduling\DTO;

class EngineRules
{
    public function __construct(
        public readonly int $slotDurationMinutes = 50,
        public readonly int $minutesPerCredit = 50,
        public readonly int $workStartHour = 8,
        public readonly int $workEndHour = 16,
        public readonly int $minStartHour = 7,
        public readonly int $maxEndHour = 20,
        public readonly string $lunchBreakStart = '12:00',
        public readonly string $lunchBreakEnd = '13:00',
        public readonly string $fridayPrayerStart = '11:20',
        public readonly int $maxOptions = 10,
        public readonly int $minimumSuccessScore = 50,
        public readonly int $proximityWindowSlots = 2,
        public readonly int $capacityTargetGap = 10,
        public readonly int $earlyExtremeExtraPenalty = 25,
        public readonly int $lateExtremeHourlyPenalty = 10,
        public readonly int $penaltyStudentConflict = 50,
        public readonly int $penaltyLunchProximity = 15,
        public readonly int $penaltyLecturerProximity = 20,
        public readonly int $penaltyEarlyMorning = 15,
        public readonly int $penaltyLateAfternoon = 15,
        public readonly int $penaltyCapacity = 50,
        public readonly int $penaltySlotJumping = 10,
    ) {
        if (
            $slotDurationMinutes <= 0
            || $minutesPerCredit <= 0
            || $minStartHour < 0
            || $maxEndHour > 24
            || $workStartHour < 0
            || $workEndHour > 24
            || $maxOptions <= 0
            || $minStartHour >= $maxEndHour
            || $workStartHour >= $workEndHour
            || $minimumSuccessScore < 0
            || $minimumSuccessScore > 100
            || $proximityWindowSlots <= 0
            || $capacityTargetGap <= 0
            || ! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $fridayPrayerStart)
            || min(
                $penaltyStudentConflict,
                $penaltyLunchProximity,
                $penaltyLecturerProximity,
                $penaltyEarlyMorning,
                $penaltyLateAfternoon,
                $penaltyCapacity,
                $penaltySlotJumping,
                $earlyExtremeExtraPenalty,
                $lateExtremeHourlyPenalty,
            ) < 0
        ) {
            throw new \InvalidArgumentException('Scheduling rules must contain valid positive ranges and non-negative penalties.');
        }
    }

    public function toArray(): array
    {
        return [
            'slot_duration_minutes' => $this->slotDurationMinutes,
            'minutes_per_credit' => $this->minutesPerCredit,
            'work_start_hour' => $this->workStartHour,
            'work_end_hour' => $this->workEndHour,
            'min_start_hour' => $this->minStartHour,
            'max_end_hour' => $this->maxEndHour,
            'lunch_break_start' => $this->lunchBreakStart,
            'lunch_break_end' => $this->lunchBreakEnd,
            'friday_prayer_start' => $this->fridayPrayerStart,
            'max_options' => $this->maxOptions,
            'minimum_success_score' => $this->minimumSuccessScore,
            'proximity_window_slots' => $this->proximityWindowSlots,
            'capacity_target_gap' => $this->capacityTargetGap,
            'penalties' => [
                'student_conflict' => $this->penaltyStudentConflict,
                'lunch_proximity' => $this->penaltyLunchProximity,
                'lecturer_proximity' => $this->penaltyLecturerProximity,
                'early_morning' => $this->penaltyEarlyMorning,
                'late_afternoon' => $this->penaltyLateAfternoon,
                'capacity' => $this->penaltyCapacity,
                'slot_jumping' => $this->penaltySlotJumping,
            ],
        ];
    }
}
