<?php

namespace App\Domain\Scheduling\Engine;

use App\Domain\Scheduling\DTO\CodeFactor;
use App\Domain\Scheduling\DTO\EngineResult;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\ScheduleOption;
use App\Domain\Scheduling\Enums\DayOfWeek;

class SchedulingEngine
{
    private array $thinkingLog = [];
    private int $thinkingCode = 0;

    private const SLOT_DURATION_MINUTES = 50;
    private const MINUTES_PER_CREDIT = 50;
    private const WORK_START_HOUR = '08:00';
    private const WORK_END_HOUR = '16:00';
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

    public function evaluate(
        RescheduleRequest $request,
        array $rooms,
        array $schedules,
        array $subjects,
        array $students,
        array $studentSubjects
    ): EngineResult {
        $this->thinkingLog = [];
        $this->thinkingCode = 0;

        $targetSchedule = $this->findByKey($schedules, 'id', $request->scheduleId);
        if (!$targetSchedule) {
            return new EngineResult(
                success: false,
                thinkingLog: "Schedule ID {$request->scheduleId} not found",
                options: []
            );
        }

        $subject = $this->findByKey($subjects, 'id', $targetSchedule['subjectID']);
        $lecturerId = $targetSchedule['lecturerID'];
        $enrolledStudentIds = $this->getListbyKey($studentSubjects, 'subjectID', $targetSchedule['subjectID']);
        $totalStudents = count($enrolledStudentIds);
        $requiredSlots = $this->calculateRequiredSlots($subject['credits']);

        $this->logStep(1, 'Ambil bahan', [
            'Mata kuliah: ' => $request->scheduleName,
            'subject' => $subject['name'],
            'credits' => $subject['credits'],
            'lecturer_id' => $lecturerId,
            'total_students' => $totalStudents,
            'required_slots' => $requiredSlots,
            'slot_duration' => self::SLOT_DURATION_MINUTES . ' menit',
        ]);

        $timeSlots = $this->generateTimeSlots();
        $lecturerSchedules = $this->getSchedulesByLecturer($schedules, $lecturerId);
        $studentSchedules = $this->getStudentSchedules($schedules, $enrolledStudentIds, $subjects);

        $bitmasks = $this->buildBitmasks(
            $timeSlots,
            $lecturerSchedules,
            $studentSchedules
        );

        $this->logStep(2, 'Bangun bitmask', [
            'total_days' => count($bitmasks),
            'slots_per_day' => count($timeSlots),
        ]);

        $candidates = $this->findConsecutiveSlots($bitmasks, $requiredSlots, $timeSlots);

        $this->logStep(3, 'Cari jendela slot', [
            'candidates_found' => count($candidates),
        ]);

        $candidatesWithJumping = $this->findSlotsWithJumping($bitmasks, $requiredSlots, $timeSlots);

        $this->logStep('3b', 'Slot jumping (lompati istirahat)', [
            'candidates_with_jumping' => count($candidatesWithJumping),
        ]);

        $allCandidates = array_merge($candidates, $candidatesWithJumping);

        $filteredByRoom = $this->filterByRoom($allCandidates, $rooms, $totalStudents);

        $this->logStep(4, 'Filter ruang', [
            'available_rooms' => count($rooms),
            'after_filter' => count($filteredByRoom),
        ]);

        $scored = $this->scoreOptions($filteredByRoom, $enrolledStudentIds, $totalStudents, $timeSlots, $lecturerSchedules, $lecturerId);

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

    private function getListbyKey(array $data, string $key, mixed $value): array
    {
        return array_filter($data, fn($item) => $item[$key] === $value);
    }

    private function calculateRequiredSlots(int $credits): int
    {
        $totalMinutes = $credits * self::MINUTES_PER_CREDIT;
        return (int) ceil($totalMinutes / self::SLOT_DURATION_MINUTES);
    }

    private function getSchedulesByLecturer(array $schedules, int $lecturerId): array
    {
        return array_filter($schedules, fn($s) => $s['lecturerID'] === $lecturerId);
    }

    private function getStudentSchedules(array $schedules, array $studentIds, array $subjects): array
    {
        return array_filter($schedules, function($schedule) use ($studentIds, $subjects) {
            foreach ($studentIds as $studentId) {
                if ($schedule['subjectID'] === $studentId) {
                    return true;
                }
            }
            return false;
        });
    }

    private function generateTimeSlots(): array
    {
        $slots = [];
        $totalSlots = 16;

        for ($i = 0; $i < $totalSlots; $i++) {
            $startMinutes = self::WORK_START_HOUR * 60 + ($i * self::SLOT_DURATION_MINUTES);
            $endMinutes = $startMinutes + self::SLOT_DURATION_MINUTES;

            $startHourSlot = intdiv($startMinutes, 60);
            $startMin = $startMinutes % 60;
            $endHourSlot = intdiv($endMinutes, 60);
            $endMin = $endMinutes % 60;

            $startTime = sprintf('%02d:%02d', $startHourSlot, $startMin);
            $endTime = sprintf('%02d:%02d', $endHourSlot, $endMin);

            $isBlocked = false;
            $blockReason = null;

            if ($startTime >= self::LUNCH_BREAK_START && $startTime < self::LUNCH_BREAK_END) {
                $isBlocked = true;
                $blockReason = 'lunch_break';
            }

            $slots[] = [
                'slot_index' => $i,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_blocked' => $isBlocked,
                'block_reason' => $blockReason,
            ];
        }

        return $slots;
    }

    private function buildBitmasks(
        array $timeSlots,
        array $lecturerSchedules,
        array $studentSchedules
    ): array {
        $bitmasks = [];

        foreach (DayOfWeek::cases() as $day) {
            $bitmask = 0;

            foreach ($timeSlots as $slot) {
                if ($this->isSlotBlocked($slot, $day)) {
                    $bitmask |= (1 << $slot['slot_index']);
                }
            }

            foreach ($lecturerSchedules as $schedule) {
                if ($schedule['day'] === $day->value) {
                    for ($i = $schedule['startSlot']; $i <= $schedule['endSlot']; $i++) {
                        if (!$this->isLunchBreakSlot($timeSlots[$i])) {
                            $bitmask |= (1 << $i);
                        }
                    }
                }
            }

            foreach ($studentSchedules as $schedule) {
                if ($schedule['day'] === $day->value) {
                    for ($i = $schedule['startSlot']; $i <= $schedule['endSlot']; $i++) {
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

                if (($bitmask & $windowMask) === 0) {
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
        }

        return $candidates;
    }

    private function findSlotsWithJumping(array $bitmasks, int $requiredSlots, array $timeSlots): array
    {
        $candidates = [];
        $lunchSlotIndex = $this->findSlotByTime($timeSlots, self::LUNCH_BREAK_START);

        foreach ($bitmasks as $dayValue => $bitmask) {
            $day = DayOfWeek::from($dayValue);

            for ($i = 0; $i <= count($timeSlots) - $requiredSlots; $i++) {
                $jumpedSlots = [];
                $validSlots = 0;

                for ($j = 0; $j < $requiredSlots; $j++) {
                    $slotIndex = $i + $j;
                    if ($slotIndex >= count($timeSlots)) break;

                    if ($this->isLunchBreakSlot($timeSlots[$slotIndex])) {
                        $jumpedSlots[] = $slotIndex;
                    } else {
                        $bit = 1 << $slotIndex;
                        if (($bitmask & $bit) === 0) {
                            $validSlots++;
                        }
                    }
                }

                if (count($jumpedSlots) > 0 && $validSlots === $requiredSlots) {
                    $startSlot = $timeSlots[$i];
                    $lastSlotIndex = $i + $requiredSlots - 1 + count($jumpedSlots);
                    if ($lastSlotIndex >= count($timeSlots)) continue;

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
                if ($room['capacity'] >= $totalStudents) {
                    $filtered[] = array_merge($candidate, [
                        'room_id' => $room['id'],
                        'room_name' => $room['name'],
                        'room_capacity' => $room['capacity'],
                    ]);
                }
            }
        }

        return $filtered;
    }

    private function scoreOptions(array $candidates, array $enrolledStudentIds, int $totalStudents, array $timeSlots, array $lecturerSchedules, int $lecturerId): array
    {
        $scored = [];

        foreach ($candidates as $candidate) {
            $codeFactors = [];
            $totalPenalty = 0;

            $conflictResult = $this->calculateStudentConflictPenalty($candidate, $enrolledStudentIds, $totalStudents, $timeSlots);
            if ($conflictResult['penalty'] > 0) {
                $codeFactors[] = $conflictResult['factor'];
                $totalPenalty += $conflictResult['penalty'];
            }

            $lunchResult = $this->calculateLunchProximityPenalty($candidate, $timeSlots);
            if ($lunchResult['penalty'] > 0) {
                $codeFactors[] = $lunchResult['factor'];
                $totalPenalty += $lunchResult['penalty'];
            }

            $lecturerResult = $this->calculateLecturerProximityPenalty($candidate, $lecturerSchedules, $lecturerId);
            if ($lecturerResult['penalty'] > 0) {
                $codeFactors[] = $lecturerResult['factor'];
                $totalPenalty += $lecturerResult['penalty'];
            }

            $earlyResult = $this->calculateEarlyMorningPenalty($candidate);
            if ($earlyResult['penalty'] > 0) {
                $codeFactors[] = $earlyResult['factor'];
                $totalPenalty += $earlyResult['penalty'];
            }

            $lateResult = $this->calculateLateAfternoonPenalty($candidate);
            if ($lateResult['penalty'] > 0) {
                $codeFactors[] = $lateResult['factor'];
                $totalPenalty += $lateResult['penalty'];
            }

            $capacityResult = $this->calculateCapacityPenalty($candidate, $totalStudents);
            if ($capacityResult['penalty'] > 0) {
                $codeFactors[] = $capacityResult['factor'];
                $totalPenalty += $capacityResult['penalty'];
            }

            $jumpingResult = $this->calculateSlotJumpingPenalty($candidate);
            if ($jumpingResult['penalty'] > 0) {
                $codeFactors[] = $jumpingResult['factor'];
                $totalPenalty += $jumpingResult['penalty'];
            }

            $score = max(0, 100 - $totalPenalty);

            $scored[] = array_merge($candidate, [
                'score' => $score,
                'code_factors' => $codeFactors,
            ]);
        }

        return $scored;
    }

    private function calculateStudentConflictPenalty(array $candidate, array $enrolledStudentIds, int $totalStudents, array $timeSlots): array
    {
        $conflictCount = 0;
        $conflictStudentIds = [];

        foreach ($enrolledStudentIds as $studentId) {
            if ($this->studentHasConflict($studentId, $candidate, $timeSlots)) {
                $conflictCount++;
                $conflictStudentIds[] = $studentId;
            }
        }

        $penalty = 0;
        $factor = null;

        if ($conflictCount > 0) {
            $conflictRatio = $conflictCount / $totalStudents;
            $penalty = (int) round(self::PENALTY_STUDENT_CONFLICT * $conflictRatio);
            $factor = new CodeFactor(
                code: 1,
                label: 'Mahasiswa bentrok',
                penalty: $penalty,
                details: [
                    'count' => $conflictCount,
                    'student_ids' => $conflictStudentIds,
                ]
            );
        }

        return ['penalty' => $penalty, 'factor' => $factor];
    }

    private function studentHasConflict(int $studentId, array $candidate, array $timeSlots): bool
    {
        return false;
    }

    private function calculateLunchProximityPenalty(array $candidate, array $timeSlots): array
    {
        $lunchStartSlot = $this->findSlotByTime($timeSlots, self::LUNCH_BREAK_START);
        $distance = abs($candidate['start_slot'] - $lunchStartSlot);

        $penalty = 0;
        $factor = null;

        if ($distance <= 2) {
            $penalty = (int) round(self::PENALTY_LUNCH_PROXIMITY * (1 - $distance / 2));
            $factor = new CodeFactor(
                code: 2,
                label: 'Dekat jam istirahat',
                penalty: $penalty,
                details: [
                    'distance_slots' => $distance,
                ]
            );
        }

        return ['penalty' => $penalty, 'factor' => $factor];
    }

    private function calculateLecturerProximityPenalty(array $candidate, array $lecturerSchedules, int $lecturerId): array
    {
        $minDistance = PHP_INT_MAX;
        $nearbySubjectId = null;

        foreach ($lecturerSchedules as $schedule) {
            if ($schedule['lecturerID'] === $lecturerId && $schedule['day'] === $candidate['day']->value) {
                $distance = min(
                    abs($candidate['start_slot'] - $schedule['endSlot'] - 1),
                    abs($schedule['startSlot'] - $candidate['end_slot'] - 1)
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearbySubjectId = $schedule['subjectID'];
                }
            }
        }

        $penalty = 0;
        $factor = null;

        if ($minDistance <= 2 && $nearbySubjectId !== null) {
            $penalty = (int) round(self::PENALTY_LECTURER_PROXIMITY * (1 - $minDistance / 2));
            $factor = new CodeFactor(
                code: 3,
                label: 'Dekat matkul dosen',
                penalty: $penalty,
                details: [
                    'distance_slots' => $minDistance,
                    'subject_id' => $nearbySubjectId,
                ]
            );
        }

        return ['penalty' => $penalty, 'factor' => $factor];
    }

    private function calculateEarlyMorningPenalty(array $candidate): array
    {
        $startHour = (int) explode(':', $candidate['start_time'])[0];

        $penalty = 0;
        $factor = null;

        if ($startHour < 10) {
            $penalty = (int) round(self::PENALTY_EARLY_MORNING * (1 - ($startHour - self::WORK_START_HOUR) / 2));
            $penalty = max(0, $penalty);
            $factor = new CodeFactor(
                code: 4,
                label: 'Jam terlalu pagi',
                penalty: $penalty,
                details: [
                    'start_hour' => $startHour,
                ]
            );
        }

        return ['penalty' => $penalty, 'factor' => $factor];
    }

    private function calculateLateAfternoonPenalty(array $candidate): array
    {
        $endHour = (int) explode(':', $candidate['end_time'])[0];
        $endMinute = (int) explode(':', $candidate['end_time'])[1];

        $penalty = 0;
        $factor = null;

        if ($endHour >= 14) {
            $hoursBeforeEnd = (self::WORK_END_HOUR - $endHour) - ($endMinute / 60);
            $penalty = (int) round(self::PENALTY_LATE_AFTERNOON * (1 - max(0, $hoursBeforeEnd) / 2));
            $penalty = max(0, min(self::PENALTY_LATE_AFTERNOON, $penalty));
            $factor = new CodeFactor(
                code: 5,
                label: 'Jam terlalu sore',
                penalty: $penalty,
                details: [
                    'end_time' => $candidate['end_time'],
                ]
            );
        }

        return ['penalty' => $penalty, 'factor' => $factor];
    }

    private function calculateCapacityPenalty(array $candidate, int $totalStudents): array
    {
        $capacity = $candidate['room_capacity'];
        $difference = $capacity - $totalStudents;

        $penalty = 0;
        $factor = null;

        if ($difference > 10) {
            $penalty = (int) round(self::PENALTY_CAPACITY * (1 - 10 / $difference));
            $penalty = max(0, min(self::PENALTY_CAPACITY, $penalty));
            $factor = new CodeFactor(
                code: 6,
                label: 'Kesesuaian kapasitas',
                penalty: $penalty,
                details: [
                    'room_capacity' => $capacity,
                    'total_students' => $totalStudents,
                    'difference' => $difference,
                ]
            );
        }

        return ['penalty' => $penalty, 'factor' => $factor];
    }

    private function calculateSlotJumpingPenalty(array $candidate): array
    {
        $penalty = 0;
        $factor = null;

        if ($candidate['has_jumping'] && count($candidate['jumped_slots']) > 0) {
            $penalty = self::PENALTY_SLOT_JUMPING * count($candidate['jumped_slots']);
            $factor = new CodeFactor(
                code: 7,
                label: 'Melewati jam istirahat',
                penalty: $penalty,
                details: [
                    'jumped_slots' => $candidate['jumped_slots'],
                    'count' => count($candidate['jumped_slots']),
                ]
            );
        }

        return ['penalty' => $penalty, 'factor' => $factor];
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
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $log .= "- {$key}: {$value}\n";
        }
        $this->thinkingLog[] = rtrim($log);
    }

    /**
     * Generic finder mendelegasikan loop ke native C level.
     * Menggunakan array_column untuk mapping dan array_search untuk indeks lookup.
     */
    private function findByKey(array $data, string $key, mixed $value): ?array
    {
        $index = array_search($value, array_column($data, $key));
        
        return $index !== false ? $data[$index] : null;
    }


}
