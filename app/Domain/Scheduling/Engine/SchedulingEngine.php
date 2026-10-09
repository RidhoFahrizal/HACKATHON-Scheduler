<?php

namespace App\Domain\Scheduling\Engine;

use App\Domain\Scheduling\DTO\CodeFactor;
use App\Domain\Scheduling\DTO\EngineInput;
use App\Domain\Scheduling\DTO\EngineResult;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\RoomDto;
use App\Domain\Scheduling\DTO\ScheduleDto;
use App\Domain\Scheduling\DTO\ScheduleOption;
use App\Domain\Scheduling\DTO\StudentSubjectDto;
use App\Domain\Scheduling\DTO\SubjectDto;
use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\Enums\Scope;

class SchedulingEngine
{
    private array $thinkingLog = [];

    private const SLOT_DURATION_MINUTES = 50;
    private const MINUTES_PER_CREDIT = 50;
    private const WORK_START_HOUR = 8;
    private const WORK_END_HOUR = 16;
    private const LUNCH_BREAK_START = '12:00';
    private const LUNCH_BREAK_END = '13:00';
    private const FRIDAY_PRAYER_START = '11:20';
    private const MAX_OPTIONS = 10;

    private const PENALTY_STUDENT_CONFLICT = 50;
    private const PENALTY_LUNCH_PROXIMITY = 15;
    private const PENALTY_LECTURER_PROXIMITY = 20;
    private const PENALTY_EARLY_MORNING = 15;
    private const PENALTY_LATE_AFTERNOON = 15;
    private const PENALTY_CAPACITY = 50;
    private const PENALTY_SLOT_JUMPING = 10;

    public function __construct() {}

    public function evaluate(RescheduleRequest $request, EngineInput $input): EngineResult
    {
        $this->thinkingLog = [];

        $this->logStep(1, 'Ambil bahan', array_merge($input->toArray(), [
            'scope' => $request->scope->value,
            'target_week' => $request->targetWeek,
            'weeks_to_evaluate' => $request->weeksToEvaluate,
        ]));

        $timeSlots = $this->generateTimeSlots();

        $bitmasks = $this->buildBitmasks(
            $timeSlots,
            $input->lecturerSchedules,
            $input->studentSchedules
        );

        $this->logStep(2, 'Bangun bitmask', [
            'total_days' => count($bitmasks),
            'slots_per_day' => count($timeSlots),
        ]);

        $candidates = $this->findConsecutiveSlots($bitmasks, $input->requiredSlots, $timeSlots);

        $this->logStep(3, 'Cari jendela slot', [
            'candidates_found' => count($candidates),
        ]);

        $candidatesWithJumping = $this->findSlotsWithJumping($bitmasks, $input->requiredSlots, $timeSlots);

        $this->logStep('3b', 'Slot jumping (lompati istirahat)', [
            'candidates_with_jumping' => count($candidatesWithJumping),
        ]);

        $allCandidates = array_merge($candidates, $candidatesWithJumping);

        $filteredByRoom = $this->filterByRoom($allCandidates, $input->rooms, $input->totalStudents);

        $this->logStep(4, 'Filter ruang', [
            'available_rooms' => count($input->rooms),
            'after_filter' => count($filteredByRoom),
        ]);

        if ($request->scope === Scope::ONWARDS) {
            $filteredByRoom = $this->verifyOnwardsPattern($filteredByRoom, $bitmasks, $request->weeksToEvaluate);

            $this->logStep('4b', 'Verifikasi pola mingguan (ONWARDS)', [
                'weeks_checked' => count($request->weeksToEvaluate),
                'after_verification' => count($filteredByRoom),
            ]);
        }

        $scored = $this->scoreOptions(
            $filteredByRoom,
            $input->enrolledStudents,
            $input->totalStudents,
            $timeSlots,
            $input->lecturerSchedules,
            $input->targetSchedule->lecturerId
        );

        $this->logStep(5, 'Scoring', [
            'total_scored' => count($scored),
        ]);

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        $topOptions = array_slice($scored, 0, self::MAX_OPTIONS);

        $options = array_map(fn($item) => $this->createScheduleOption($item), $topOptions);

        $success = count($options) > 0 && $options[0]->score >= 50;

        return new EngineResult(
            success: $success,
            thinkingLog: implode("\n\n", $this->thinkingLog),
            options: $options,
        );
    }

    public static function buildInput(
        string $scheduleId,
        array $rooms,
        array $schedules,
        array $subjects,
        array $studentSubjects
    ): ?EngineInput {
        $targetSchedule = self::findById($schedules, $scheduleId);
        if (!$targetSchedule) {
            return null;
        }

        $subject = self::findById($subjects, $targetSchedule->subjectId);
        if (!$subject) {
            return null;
        }

        $enrolledStudents = array_values(array_filter(
            $studentSubjects,
            fn($s) => $s->subjectId === $subject->id
        ));

        $totalStudents = count($enrolledStudents);
        $requiredSlots = self::calculateRequiredSlots($subject->credits);

        $lecturerSchedules = array_values(array_filter(
            $schedules,
            fn($s) => $s->lecturerId === $targetSchedule->lecturerId
        ));

        $enrolledSubjectIds = array_map(fn($s) => $s->subjectId, $enrolledStudents);
        $studentSchedules = array_values(array_filter(
            $schedules,
            fn($s) => in_array($s->subjectId, $enrolledSubjectIds)
        ));

        return new EngineInput(
            targetSchedule: $targetSchedule,
            subject: $subject,
            lecturerSchedules: $lecturerSchedules,
            studentSchedules: $studentSchedules,
            enrolledStudents: $enrolledStudents,
            totalStudents: $totalStudents,
            requiredSlots: $requiredSlots,
            rooms: $rooms,
        );
    }

    private static function findById(array $items, string $id): ?object
    {
        foreach ($items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }
        return null;
    }

    private static function calculateRequiredSlots(int $credits): int
    {
        $totalMinutes = $credits * self::MINUTES_PER_CREDIT;
        return (int) ceil($totalMinutes / self::SLOT_DURATION_MINUTES);
    }

    private function verifyOnwardsPattern(array $candidates, array $bitmasks, array $weeksToEvaluate): array
    {
        $verified = [];

        foreach ($candidates as $candidate) {
            $dayValue = $candidate['day']->value;
            $bitmask = $bitmasks[$dayValue] ?? 0;

            $windowMask = 0;
            for ($j = $candidate['start_slot']; $j <= $candidate['end_slot']; $j++) {
                $windowMask |= (1 << $j);
            }

            if (($bitmask & $windowMask) === 0) {
                $candidate['verified_weeks'] = $weeksToEvaluate;
                $verified[] = $candidate;
            }
        }

        return $verified;
    }

    private function generateTimeSlots(): array
    {
        $slots = [];
        $totalSlots = 16;

        for ($i = 0; $i < $totalSlots; $i++) {
            $startMinutes = self::WORK_START_HOUR * 60 + ($i * self::SLOT_DURATION_MINUTES);
            $endMinutes = $startMinutes + self::SLOT_DURATION_MINUTES;

            $startTime = sprintf('%02d:%02d', intdiv($startMinutes, 60), $startMinutes % 60);
            $endTime = sprintf('%02d:%02d', intdiv($endMinutes, 60), $endMinutes % 60);

            $isBlocked = $startTime >= self::LUNCH_BREAK_START && $startTime < self::LUNCH_BREAK_END;

            $slots[] = [
                'slot_index' => $i,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_blocked' => $isBlocked,
                'block_reason' => $isBlocked ? 'lunch_break' : null,
            ];
        }

        return $slots;
    }

    private function buildBitmasks(array $timeSlots, array $lecturerSchedules, array $studentSchedules): array
    {
        $bitmasks = [];

        foreach (DayOfWeek::cases() as $day) {
            $bitmask = 0;

            foreach ($timeSlots as $slot) {
                if ($this->isSlotBlocked($slot, $day)) {
                    $bitmask |= (1 << $slot['slot_index']);
                }
            }

            foreach ($lecturerSchedules as $schedule) {
                if ($schedule->day === $day->value) {
                    for ($i = $schedule->startSlot; $i <= $schedule->endSlot; $i++) {
                        if (!$this->isLunchBreakSlot($timeSlots[$i])) {
                            $bitmask |= (1 << $i);
                        }
                    }
                }
            }

            foreach ($studentSchedules as $schedule) {
                if ($schedule->day === $day->value) {
                    for ($i = $schedule->startSlot; $i <= $schedule->endSlot; $i++) {
                        if (!$this->isLunchBreakSlot($timeSlots[$i])) {
                            $bitmask |= (1 << $i);
                        }
                    }
                }
            }

            $bitmasks[$day->value] = $bitmask;
        }

        return $bitmasks;
    }

    private function isLunchBreakSlot(array $slot): bool
    {
        return $slot['is_blocked'] && $slot['block_reason'] === 'lunch_break';
    }

    private function isSlotBlocked(array $slot, DayOfWeek $day): bool
    {
        if ($slot['is_blocked']) {
            return true;
        }

        if ($day->isFriday() && $slot['start_time'] >= self::FRIDAY_PRAYER_START && $slot['start_time'] < self::LUNCH_BREAK_END) {
            return true;
        }

        return false;
    }

    private function findConsecutiveSlots(array $bitmasks, int $requiredSlots, array $timeSlots): array
    {
        $candidates = [];

        foreach ($bitmasks as $dayValue => $bitmask) {
            $day = DayOfWeek::from($dayValue);

            for ($i = 0; $i <= count($timeSlots) - $requiredSlots; $i++) {
                $windowMask = 0;
                for ($j = 0; $j < $requiredSlots; $j++) {
                    $windowMask |= (1 << ($i + $j));
                }

                if (($bitmask & $windowMask) !== 0) {
                    continue;
                }

                $startSlot = $timeSlots[$i];
                $endSlot = $timeSlots[$i + $requiredSlots - 1];

                if ($day->isFriday() && $endSlot['end_time'] > self::FRIDAY_PRAYER_START) {
                    continue;
                }

                if ((int) explode(':', $startSlot['start_time'])[0] < self::WORK_START_HOUR) {
                    continue;
                }

                if ((int) explode(':', $endSlot['end_time'])[0] > self::WORK_END_HOUR) {
                    continue;
                }

                $candidates[] = [
                    'day' => $day,
                    'start_slot' => $i,
                    'end_slot' => $i + $requiredSlots - 1,
                    'start_time' => $startSlot['start_time'],
                    'end_time' => $endSlot['end_time'],
                    'has_jumping' => false,
                    'jumped_slots' => [],
                ];
            }
        }

        return $candidates;
    }

    private function findSlotsWithJumping(array $bitmasks, int $requiredSlots, array $timeSlots): array
    {
        $candidates = [];
        $totalSlots = count($timeSlots);

        foreach ($bitmasks as $dayValue => $bitmask) {
            $day = DayOfWeek::from($dayValue);

            for ($i = 0; $i < $totalSlots; $i++) {
                $jumpedSlots = [];
                $validSlots = 0;
                $j = 0;

                while ($validSlots < $requiredSlots && ($i + $j) < $totalSlots) {
                    $slotIndex = $i + $j;

                    if ($this->isLunchBreakSlot($timeSlots[$slotIndex])) {
                        $jumpedSlots[] = $slotIndex;
                    } else {
                        if (($bitmask & (1 << $slotIndex)) === 0) {
                            $validSlots++;
                        } else {
                            break;
                        }
                    }
                    $j++;
                }

                if (count($jumpedSlots) > 0 && $validSlots === $requiredSlots) {
                    $startSlot = $timeSlots[$i];
                    $lastSlotIndex = $i + $j - 1;
                    $endSlot = $timeSlots[$lastSlotIndex];

                    if ($day->isFriday() && $endSlot['end_time'] > self::FRIDAY_PRAYER_START) {
                        continue;
                    }

                    if ((int) explode(':', $startSlot['start_time'])[0] < self::WORK_START_HOUR) {
                        continue;
                    }

                    if ((int) explode(':', $endSlot['end_time'])[0] > self::WORK_END_HOUR) {
                        continue;
                    }

                    $candidates[] = [
                        'day' => $day,
                        'start_slot' => $i,
                        'end_slot' => $lastSlotIndex,
                        'start_time' => $startSlot['start_time'],
                        'end_time' => $endSlot['end_time'],
                        'has_jumping' => true,
                        'jumped_slots' => $jumpedSlots,
                    ];
                }
            }
        }

        return $candidates;
    }

    private function filterByRoom(array $candidates, array $rooms, int $totalStudents): array
    {
        $filtered = [];

        foreach ($candidates as $candidate) {
            foreach ($rooms as $room) {
                if ($room->capacity >= $totalStudents) {
                    $filtered[] = array_merge($candidate, [
                        'room_id' => $room->id,
                        'room_name' => $room->name,
                        'room_capacity' => $room->capacity,
                    ]);
                }
            }
        }

        return $filtered;
    }

    private function scoreOptions(array $candidates, array $enrolledStudents, int $totalStudents, array $timeSlots, array $lecturerSchedules, string $lecturerId): array
    {
        $scored = [];

        foreach ($candidates as $candidate) {
            $codeFactors = [];
            $totalPenalty = 0;

            foreach ([
                $this->calculateStudentConflictPenalty($candidate, $enrolledStudents, $totalStudents),
                $this->calculateLunchProximityPenalty($candidate, $timeSlots),
                $this->calculateLecturerProximityPenalty($candidate, $lecturerSchedules, $lecturerId),
                $this->calculateEarlyMorningPenalty($candidate),
                $this->calculateLateAfternoonPenalty($candidate),
                $this->calculateCapacityPenalty($candidate, $totalStudents),
                $this->calculateSlotJumpingPenalty($candidate),
            ] as $result) {
                if ($result['penalty'] > 0) {
                    $codeFactors[] = $result['factor'];
                    $totalPenalty += $result['penalty'];
                }
            }

            $scored[] = array_merge($candidate, [
                'score' => max(0, 100 - $totalPenalty),
                'code_factors' => $codeFactors,
            ]);
        }

        return $scored;
    }

    private function calculateStudentConflictPenalty(array $candidate, array $enrolledStudents, int $totalStudents): array
    {
        $conflictCount = 0;
        $conflictStudentIds = [];

        foreach ($enrolledStudents as $enrollment) {
            if ($this->studentHasConflict($enrollment->studentId, $candidate)) {
                $conflictCount++;
                $conflictStudentIds[] = $enrollment->studentId;
            }
        }

        if ($conflictCount === 0) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = (int) round(self::PENALTY_STUDENT_CONFLICT * ($conflictCount / $totalStudents));

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(1, 'Mahasiswa bentrok', $penalty, [
                'count' => $conflictCount,
                'student_ids' => $conflictStudentIds,
            ]),
        ];
    }

    private function studentHasConflict(string $studentId, array $candidate): bool
    {
        return false;
    }

    private function calculateLunchProximityPenalty(array $candidate, array $timeSlots): array
    {
        $lunchStartSlot = $this->findSlotByTime($timeSlots, self::LUNCH_BREAK_START);
        $distance = abs($candidate['start_slot'] - $lunchStartSlot);

        if ($distance > 2) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = (int) round(self::PENALTY_LUNCH_PROXIMITY * (1 - $distance / 2));

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(2, 'Dekat jam istirahat', $penalty, [
                'distance_slots' => $distance,
            ]),
        ];
    }

    private function calculateLecturerProximityPenalty(array $candidate, array $lecturerSchedules, string $lecturerId): array
    {
        $minDistance = PHP_INT_MAX;
        $nearbySubjectId = null;

        foreach ($lecturerSchedules as $schedule) {
            if ($schedule->lecturerId !== $lecturerId || $schedule->day !== $candidate['day']->value) {
                continue;
            }

            $distance = min(
                abs($candidate['start_slot'] - $schedule->endSlot - 1),
                abs($schedule->startSlot - $candidate['end_slot'] - 1)
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearbySubjectId = $schedule->subjectId;
            }
        }

        if ($minDistance > 2 || $nearbySubjectId === null) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = (int) round(self::PENALTY_LECTURER_PROXIMITY * (1 - $minDistance / 2));

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(3, 'Dekat matkul dosen', $penalty, [
                'distance_slots' => $minDistance,
                'subject_id' => $nearbySubjectId,
            ]),
        ];
    }

    private function calculateEarlyMorningPenalty(array $candidate): array
    {
        $startHour = (int) explode(':', $candidate['start_time'])[0];

        if ($startHour >= 10) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = max(0, (int) round(self::PENALTY_EARLY_MORNING * (1 - ($startHour - self::WORK_START_HOUR) / 2)));

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(4, 'Jam terlalu pagi', $penalty, [
                'start_hour' => $startHour,
            ]),
        ];
    }

    private function calculateLateAfternoonPenalty(array $candidate): array
    {
        $endHour = (int) explode(':', $candidate['end_time'])[0];
        $endMinute = (int) explode(':', $candidate['end_time'])[1];

        if ($endHour < 14) {
            return ['penalty' => 0, 'factor' => null];
        }

        $hoursBeforeEnd = max(0, (self::WORK_END_HOUR - $endHour) - ($endMinute / 60));
        $penalty = max(0, min(self::PENALTY_LATE_AFTERNOON, (int) round(self::PENALTY_LATE_AFTERNOON * (1 - $hoursBeforeEnd / 2))));

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(5, 'Jam terlalu sore', $penalty, [
                'end_time' => $candidate['end_time'],
            ]),
        ];
    }

    private function calculateCapacityPenalty(array $candidate, int $totalStudents): array
    {
        $difference = $candidate['room_capacity'] - $totalStudents;

        if ($difference <= 10) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = max(0, min(self::PENALTY_CAPACITY, (int) round(self::PENALTY_CAPACITY * (1 - 10 / $difference))));

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(6, 'Kesesuaian kapasitas', $penalty, [
                'room_capacity' => $candidate['room_capacity'],
                'total_students' => $totalStudents,
                'difference' => $difference,
            ]),
        ];
    }

    private function calculateSlotJumpingPenalty(array $candidate): array
    {
        if (!$candidate['has_jumping'] || count($candidate['jumped_slots']) === 0) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = self::PENALTY_SLOT_JUMPING * count($candidate['jumped_slots']);

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(7, 'Melewati jam istirahat', $penalty, [
                'jumped_slots' => $candidate['jumped_slots'],
                'count' => count($candidate['jumped_slots']),
            ]),
        ];
    }

    private function findSlotByTime(array $timeSlots, string $time): int
    {
        foreach ($timeSlots as $slot) {
            if ($slot['start_time'] === $time) {
                return $slot['slot_index'];
            }
        }
        return 0;
    }

    private function createScheduleOption(array $data): ScheduleOption
    {
        return new ScheduleOption(
            day: $data['day'],
            startSlot: $data['start_slot'],
            endSlot: $data['end_slot'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            roomId: $data['room_id'],
            roomName: $data['room_name'],
            score: $data['score'],
            codeFactors: $data['code_factors'],
        );
    }

    private function logStep(int|string $stepNumber, string $title, array $details = []): void
    {
        $thinkingCode = is_int($stepNumber) ? ($stepNumber - 1) : $stepNumber;
        $log = "[STEP {$stepNumber}: {$title}] (thinking code: {$thinkingCode})\n";
        foreach ($details as $key => $value) {
            $log .= '- ' . $key . ': ' . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
        $this->thinkingLog[] = rtrim($log);
    }
}
