<?php

namespace App\Domain\Scheduling\Actions;

use App\Domain\Scheduling\DTO\EngineRules;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\RoomDto;
use App\Domain\Scheduling\DTO\ScheduleDto;
use App\Domain\Scheduling\DTO\StudentSubjectDto;
use App\Domain\Scheduling\DTO\SubjectDto;
use App\Domain\Scheduling\Engine\SchedulingEngine;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\StudentSubject;
use App\Models\Subject;
use Generator;

class EvaluateSchedulingAction
{
    private SchedulingEngine $engine;

    public function __construct(SchedulingEngine $engine)
    {
        $this->engine = $engine;
    }

    public function execute(RescheduleRequest $request): array
    {
        $buildResult = $this->buildInput($request->scheduleId);

        if (! $buildResult) {
            return [
                'success' => false,
                'error' => 'Schedule not found',
                'steps' => [],
                'options' => [],
            ];
        }

        $result = $this->engine->evaluate($request, $buildResult['input'], $buildResult['allSchedules']);

        return $result->toArray();
    }

    public function executeStream(RescheduleRequest $request): Generator
    {
        $buildResult = $this->buildInput($request->scheduleId);

        if (! $buildResult) {
            yield [
                'type' => 'error',
                'error' => 'Schedule not found',
            ];

            return;
        }

        foreach ($this->engine->evaluateStream($request, $buildResult['input'], $buildResult['allSchedules']) as $step) {
            yield $step->toArray();
        }
    }

    private function buildInput(string $scheduleId): ?array
    {
        $rooms = Room::all()
            ->map(fn ($r) => RoomDto::fromModel($r))
            ->toArray();

        $schedules = Schedule::all()
            ->map(fn ($s) => ScheduleDto::fromModel($s))
            ->toArray();

        $subjects = Subject::all()
            ->map(fn ($s) => SubjectDto::fromModel($s))
            ->toArray();

        $studentSubjects = StudentSubject::all()
            ->map(fn ($ss) => StudentSubjectDto::fromModel($ss))
            ->toArray();

        $rules = new EngineRules(
            slotDurationMinutes: (int) Setting::get('slot_duration_minutes', 50),
            minutesPerCredit: (int) Setting::get('minutes_per_credit', 50),
            workStartHour: (int) Setting::get('work_start_hour', 8),
            workEndHour: (int) Setting::get('work_end_hour', 16),
            minStartHour: (int) Setting::get('min_start_hour', 7),
            maxEndHour: (int) Setting::get('max_end_hour', 20),
            lunchBreakStart: (string) Setting::get('lunch_break_start', '12:00'),
            lunchBreakEnd: (string) Setting::get('lunch_break_end', '13:00'),
            fridayPrayerStart: (string) Setting::get('friday_prayer_start', '11:20'),
            maxOptions: (int) Setting::get('max_options', 10),
            minimumSuccessScore: (int) Setting::get('minimum_success_score', 50),
            proximityWindowSlots: (int) Setting::get('proximity_window_slots', 2),
            capacityTargetGap: (int) Setting::get('capacity_target_gap', 10),
            earlyExtremeExtraPenalty: (int) Setting::get('early_extreme_extra_penalty', 25),
            lateExtremeHourlyPenalty: (int) Setting::get('late_extreme_hourly_penalty', 10),
            penaltyStudentConflict: (int) Setting::get('penalty_student_conflict', 50),
            penaltyLunchProximity: (int) Setting::get('penalty_lunch_proximity', 15),
            penaltyLecturerProximity: (int) Setting::get('penalty_lecturer_proximity', 20),
            penaltyEarlyMorning: (int) Setting::get('penalty_early_morning', 15),
            penaltyLateAfternoon: (int) Setting::get('penalty_late_afternoon', 15),
            penaltyCapacity: (int) Setting::get('penalty_capacity', 50),
            penaltySlotJumping: (int) Setting::get('penalty_slot_jumping', 10),
        );

        return SchedulingEngine::buildInput(
            $scheduleId,
            $rooms,
            $schedules,
            $subjects,
            $studentSubjects,
            $rules,
        );
    }
}
