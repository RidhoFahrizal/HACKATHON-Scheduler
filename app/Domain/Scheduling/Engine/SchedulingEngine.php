<?php

namespace App\Domain\Scheduling\Engine;

use App\Domain\Scheduling\DTO\CodeFactor;
use App\Domain\Scheduling\DTO\EngineInput;
use App\Domain\Scheduling\DTO\EngineResult;
use App\Domain\Scheduling\DTO\EngineRules;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\ScheduleOption;
use App\Domain\Scheduling\DTO\ThinkingStep;
use App\Domain\Scheduling\Enums\DayOfWeek;
use App\Domain\Scheduling\Enums\Scope;
use App\Domain\Scheduling\ValueObjects\TimeSlotGrid;
use Generator;

class SchedulingEngine
{
    private EngineRules $rules;

    private array $steps = [];

    private array $timeSlots = [];

    public function __construct()
    {
        $this->rules = new EngineRules;
    }

    public function evaluate(RescheduleRequest $request, EngineInput $input, array $allSchedules): EngineResult
    {
        $this->assertTargetMatchesInput($request, $input);
        $this->steps = [];
        $this->rules = $input->rules;
        $this->timeSlots = $this->generateTimeSlots();

        $this->addStep(1, 'Ambil bahan', array_merge($input->toArray(), [
            'scope' => $request->scope->value,
            'target_week' => $request->targetWeek,
            'weeks_to_evaluate' => $request->weeksToEvaluate,
        ]));

        // cek jadwal
        $this->assertSchedulesFitGrid(array_merge($input->lecturerSchedules, $input->studentSchedules, $allSchedules));
        
        // bangun bitmask
        $bitmasks = $this->buildBitmasks($input->lecturerSchedules);

        $this->addStep(2, 'Bangun bitmask', [
            'total_days' => count($bitmasks),
            'slots_per_day' => count($this->timeSlots),
        ]);

        // cari jendela slot yang berurutan
        $candidates = $this->findConsecutiveSlots($bitmasks, $input->requiredSlots);

        $this->addStep(3, 'Cari jendela slot', [
            'candidates_found' => count($candidates),
        ]);

        // cari slot yang memiliki jeda (istirahat)
        $candidatesWithJumping = $this->findSlotsWithJumping($bitmasks, $input->requiredSlots);

        $this->addStep('3b', 'Slot jumping (lompati istirahat)', [
            'candidates_with_jumping' => count($candidatesWithJumping),
        ]);

        $allCandidates = array_merge($candidates, $candidatesWithJumping);
        $filteredByRoom = $this->filterByRoom($allCandidates, $input->rooms, $input->totalStudents, $allSchedules, $input->targetSchedule->id);

        $this->addStep(4, 'Filter ruang', [
            'available_rooms' => count($input->rooms),
            'after_filter' => count($filteredByRoom),
        ]);

        if ($request->scope === Scope::ONWARDS) {
            $filteredByRoom = $this->verifyOnwardsPattern($filteredByRoom, $bitmasks, $request->weeksToEvaluate);

            $this->addStep('4b', 'Verifikasi pola mingguan (ONWARDS)', [
                'weeks_checked' => count($request->weeksToEvaluate),
                'after_verification' => count($filteredByRoom),
            ]);
        }

        $scored = $this->scoreOptions($filteredByRoom, $input);

        $this->addStep(5, 'Scoring', [
            'total_scored' => count($scored),
        ]);

        usort($scored, fn ($a, $b) => $this->compareScoredOptions($a, $b));
        $topOptions = array_slice($scored, 0, $this->rules->maxOptions);
        $options = array_map(fn ($item) => $this->createScheduleOption($item), $topOptions);

        $success = count($options) > 0 && $options[0]->score >= $this->rules->minimumSuccessScore;

        return new EngineResult(
            success: $success,
            steps: $this->steps,
            options: $options,
        );
    }

    public function evaluateStream(RescheduleRequest $request, EngineInput $input, array $allSchedules): Generator
    {
        $this->assertTargetMatchesInput($request, $input);
        $this->steps = [];
        $this->rules = $input->rules;
        $this->timeSlots = $this->generateTimeSlots();

        $this->addStep(1, 'Ambil bahan', array_merge($input->toArray(), [
            'scope' => $request->scope->value,
            'target_week' => $request->targetWeek,
            'weeks_to_evaluate' => $request->weeksToEvaluate,
        ]));
        yield $this->lastStep();

        $this->assertSchedulesFitGrid(array_merge($input->lecturerSchedules, $input->studentSchedules, $allSchedules));
        $bitmasks = $this->buildBitmasks($input->lecturerSchedules);

        $this->addStep(2, 'Bangun bitmask', [
            'total_days' => count($bitmasks),
            'slots_per_day' => count($this->timeSlots),
        ]);
        yield $this->lastStep();

        $candidates = $this->findConsecutiveSlots($bitmasks, $input->requiredSlots);

        $this->addStep(3, 'Cari jendela slot', [
            'candidates_found' => count($candidates),
        ]);
        yield $this->lastStep();

        $candidatesWithJumping = $this->findSlotsWithJumping($bitmasks, $input->requiredSlots);

        $this->addStep('3b', 'Slot jumping (lompati istirahat)', [
            'candidates_with_jumping' => count($candidatesWithJumping),
        ]);
        yield $this->lastStep();

        $allCandidates = array_merge($candidates, $candidatesWithJumping);
        $filteredByRoom = $this->filterByRoom($allCandidates, $input->rooms, $input->totalStudents, $allSchedules, $input->targetSchedule->id);

        $this->addStep(4, 'Filter ruang', [
            'available_rooms' => count($input->rooms),
            'after_filter' => count($filteredByRoom),
        ]);
        yield $this->lastStep();

        if ($request->scope === Scope::ONWARDS) {
            $filteredByRoom = $this->verifyOnwardsPattern($filteredByRoom, $bitmasks, $request->weeksToEvaluate);

            $this->addStep('4b', 'Verifikasi pola mingguan (ONWARDS)', [
                'weeks_checked' => count($request->weeksToEvaluate),
                'after_verification' => count($filteredByRoom),
            ]);
            yield $this->lastStep();
        }

        $scored = $this->scoreOptions($filteredByRoom, $input);

        $this->addStep(5, 'Scoring', [
            'total_scored' => count($scored),
        ]);
        yield $this->lastStep();

        usort($scored, fn ($a, $b) => $this->compareScoredOptions($a, $b));
        $topOptions = array_slice($scored, 0, $this->rules->maxOptions);
        $options = array_map(fn ($item) => $this->createScheduleOption($item), $topOptions);

        $success = count($options) > 0 && $options[0]->score >= $this->rules->minimumSuccessScore;

        yield new ThinkingStep(
            stepNumber: 'result',
            title: 'Hasil akhir',
            thinkingCode: 'result',
            details: [
                'success' => $success,
                'options_count' => count($options),
                'top_score' => count($options) > 0 ? $options[0]->score : 0,
            ]
        );
    }

    public static function buildInput(
        string $scheduleId,
        array $rooms,
        array $schedules,
        array $subjects,
        array $allStudentSubjects,
        ?EngineRules $rules = null,
    ): ?array {
        $rules ??= new EngineRules;
        $targetSchedule = self::findById($schedules, $scheduleId);
        if (! $targetSchedule) {
            return null;
        }

        $semesterSchedules = array_values(array_filter(
            $schedules,
            fn ($schedule) => $schedule->semesterType === $targetSchedule->semesterType
        ));

        $subject = self::findById($subjects, $targetSchedule->subjectId);
        if (! $subject) {
            return null;
        }

        $enrolledStudents = array_values(array_filter(
            $allStudentSubjects,
            fn ($s) => $s->subjectId === $subject->id
        ));

        $studentIds = array_values(array_unique(
            array_map(fn ($s) => $s->studentId, $enrolledStudents)
        ));

        $allStudentSubjectIds = array_values(array_unique(
            array_map(fn ($s) => $s->subjectId, array_filter(
                $allStudentSubjects,
                fn ($s) => in_array($s->studentId, $studentIds, true)
            ))
        ));

        $studentSchedules = array_values(array_filter(
            $semesterSchedules,
            fn ($s) => in_array($s->subjectId, $allStudentSubjectIds, true)
                && $s->id !== $targetSchedule->id
        ));

        $lecturerSchedules = array_values(array_filter(
            $semesterSchedules,
            fn ($s) => $s->lecturerId === $targetSchedule->lecturerId
                && $s->id !== $targetSchedule->id
        ));

        $totalStudents = count($studentIds);
        $requiredSlots = self::calculateRequiredSlots($subject->credits, $rules);

        $input = new EngineInput(
            targetSchedule: $targetSchedule,
            subject: $subject,
            lecturerSchedules: $lecturerSchedules,
            studentSchedules: $studentSchedules,
            enrolledStudents: $enrolledStudents,
            totalStudents: $totalStudents,
            requiredSlots: $requiredSlots,
            rooms: $rooms,
            allStudentSubjects: $allStudentSubjects,
            rules: $rules,
        );

        return [
            'input' => $input,
            'allSchedules' => $semesterSchedules,
        ];
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

    private static function calculateRequiredSlots(int $credits, EngineRules $rules): int
    {
        if ($credits <= 0 || $rules->minutesPerCredit <= 0 || $rules->slotDurationMinutes <= 0) {
            throw new \InvalidArgumentException('Credits and scheduling durations must be positive.');
        }

        return (int) ceil(($credits * $rules->minutesPerCredit) / $rules->slotDurationMinutes);
    }

    private function lastStep(): ThinkingStep
    {
        return $this->steps[count($this->steps) - 1];
    }

    private function assertTargetMatchesInput(RescheduleRequest $request, EngineInput $input): void
    {
        if ($request->scheduleId !== $input->targetSchedule->id) {
            throw new \InvalidArgumentException('Request schedule ID does not match the engine input target schedule.');
        }
    }

    private function addStep(int|string $stepNumber, string $title, array $details = []): void
    {
        $thinkingCode = is_int($stepNumber) ? ($stepNumber - 1) : $stepNumber;

        $this->steps[] = new ThinkingStep(
            stepNumber: $stepNumber,
            title: $title,
            thinkingCode: $thinkingCode,
            details: $details,
        );
    }

    private function verifyOnwardsPattern(array $candidates, array $bitmasks, array $weeksToEvaluate): array
    {
        $verified = [];

        foreach ($candidates as $candidate) {
            $dayValue = $candidate['day']->value;
            $bitmask = $bitmasks[$dayValue] ?? 0;
            $jumpedSlots = $candidate['jumped_slots'] ?? [];

            $windowMask = 0;
            for ($j = $candidate['start_slot']; $j <= $candidate['end_slot']; $j++) {
                if (! in_array($j, $jumpedSlots, true)) {
                    $windowMask |= (1 << $j);
                }
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
        return TimeSlotGrid::generate(
            $this->rules->slotDurationMinutes,
            $this->rules->minStartHour,
            $this->rules->maxEndHour,
            $this->rules->lunchBreakStart,
            $this->rules->lunchBreakEnd,
        );
    }

    private function buildBitmasks(array $lecturerSchedules): array
    {
        $bitmasks = [];

        foreach (DayOfWeek::cases() as $day) {
            $bitmask = 0;

            // apakah slot jadwal terkendala (misal jam istirahat) 
            foreach ($this->timeSlots as $slot) {
                if ($this->isSlotBlocked($slot, $day)) {
                    $bitmask |= (1 << $slot['slot_index']);
                }
            }

            // apakah slot jadwal dosen bentrok dengan jadwal lain
            foreach ($lecturerSchedules as $schedule) {
                if ($schedule->day === $day->value) {
                    for ($i = $schedule->startSlot; $i <= $schedule->endSlot; $i++) {
                        if (! $this->isLunchBreakSlot($this->timeSlots[$i])) {
                            $bitmask |= (1 << $i);
                        }
                    }
                }
            }

            $bitmasks[$day->value] = $bitmask;
        }

        return $bitmasks;
    }

    private function assertSchedulesFitGrid(array $schedules): void
    {
        foreach ($schedules as $schedule) {
            if (
                DayOfWeek::tryFrom($schedule->day) === null
                || $schedule->startSlot < 0
                || $schedule->endSlot < $schedule->startSlot
                || $schedule->endSlot >= count($this->timeSlots)
            ) {
                throw new \InvalidArgumentException("Schedule {$schedule->id} has an invalid day or slot range for the configured time grid.");
            }
        }
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

        if ($day->isFriday()
            && $slot['start_time'] < $this->rules->lunchBreakEnd
            && $slot['end_time'] > $this->rules->fridayPrayerStart) {
            return true;
        }

        return false;
    }

    private function findConsecutiveSlots(array $bitmasks, int $requiredSlots): array
    {
        $candidates = [];

        // cari slot berurutan yang memenuhi syarat 
        foreach ($bitmasks as $dayValue => $bitmask) {
            $day = DayOfWeek::from($dayValue);

            // cari jendela slot berurutan yang tersedia
            for ($i = 0; $i <= count($this->timeSlots) - $requiredSlots; $i++) {
                $windowMask = 0;
                for ($j = 0; $j < $requiredSlots; $j++) {
                    $windowMask |= (1 << ($i + $j));
                }

                if (($bitmask & $windowMask) !== 0) {
                    continue;
                }

                $startSlot = $this->timeSlots[$i];
                $endSlot = $this->timeSlots[$i + $requiredSlots - 1];

                if ($day->isFriday() && $endSlot['end_time'] > $this->rules->fridayPrayerStart) {
                    continue;
                }

                if ((int) explode(':', $startSlot['start_time'])[0] < $this->rules->minStartHour) {
                    continue;
                }

                $endHour = (int) explode(':', $endSlot['end_time'])[0];
                $endMin = (int) explode(':', $endSlot['end_time'])[1];
                if ($endHour > $this->rules->maxEndHour || ($endHour === $this->rules->maxEndHour && $endMin > 0)) {
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

    private function findSlotsWithJumping(array $bitmasks, int $requiredSlots): array
    {
        $candidates = [];
        $totalSlots = count($this->timeSlots);

        foreach ($bitmasks as $dayValue => $bitmask) {
            $day = DayOfWeek::from($dayValue);

            for ($i = 0; $i < $totalSlots; $i++) {
                if ($this->isLunchBreakSlot($this->timeSlots[$i])) {
                    continue;
                }

                $jumpedSlots = [];
                $validSlots = 0;
                $j = 0;

                while ($validSlots < $requiredSlots && ($i + $j) < $totalSlots) {
                    $slotIndex = $i + $j;

                    if ($this->isLunchBreakSlot($this->timeSlots[$slotIndex])) {
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
                    $startSlot = $this->timeSlots[$i];
                    $lastSlotIndex = $i + $j - 1;
                    $endSlot = $this->timeSlots[$lastSlotIndex];

                    if ($day->isFriday() && $endSlot['end_time'] > $this->rules->fridayPrayerStart) {
                        continue;
                    }

                    if ((int) explode(':', $startSlot['start_time'])[0] < $this->rules->minStartHour) {
                        continue;
                    }

                    $endHour = (int) explode(':', $endSlot['end_time'])[0];
                    $endMin = (int) explode(':', $endSlot['end_time'])[1];
                    if ($endHour > $this->rules->maxEndHour || ($endHour === $this->rules->maxEndHour && $endMin > 0)) {
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

    private function filterByRoom(array $candidates, array $rooms, int $totalStudents, array $allSchedules, string $targetScheduleId): array
    {
        $filtered = [];

        // filter kandidat berdasarkan ketersediaan ruangan
        foreach ($candidates as $candidate) {
            foreach ($rooms as $room) {
                if ($room->capacity >= $totalStudents) {
                    $isRoomFree = true;

                    foreach ($allSchedules as $schedule) {
                        if ($schedule->id !== $targetScheduleId &&
                            $schedule->roomId === $room->id &&
                            $schedule->day === $candidate['day']->value) {

                            if ($this->slotsOverlap(
                                $candidate['start_slot'],
                                $candidate['end_slot'],
                                $schedule->startSlot,
                                $schedule->endSlot
                            )) {
                                $isRoomFree = false;
                                break;
                            }
                        }
                    }

                    if ($isRoomFree) {
                        $filtered[] = array_merge($candidate, [
                            'room_id' => $room->id,
                            'room_name' => $room->name,
                            'room_capacity' => $room->capacity,
                        ]);
                    }
                }
            }
        }

        return $filtered;
    }

    private function scoreOptions(
        array $candidates,
        EngineInput $input
    ): array {
        $scored = [];

        foreach ($candidates as $candidate) {
            $codeFactors = [];
            $totalPenalty = 0;

            foreach ([
                $this->calculateStudentConflictPenalty($candidate, $input->studentIds, $input->studentSchedules, $input->totalStudents, $input),
                $this->calculateLunchProximityPenalty($candidate),
                $this->calculateLecturerProximityPenalty($candidate, $input->lecturerSchedules, $input->targetSchedule->lecturerId),
                $this->calculateEarlyMorningPenalty($candidate),
                $this->calculateLateAfternoonPenalty($candidate),
                $this->calculateCapacityPenalty($candidate, $input->totalStudents),
                $this->calculateSlotJumpingPenalty($candidate),
            ] as $result) {
                if ($result['penalty'] > 0) {
                    $codeFactors[] = $result['factor'];
                    $totalPenalty += $result['penalty'];
                }
            }

            $scored[] = array_merge($candidate, [
                'score' => max(0, 100 - $totalPenalty),
                'capacity_gap' => $candidate['room_capacity'] - $input->totalStudents,
                'code_factors' => $codeFactors,
            ]);
        }

        return $scored;
    }

    private function calculateStudentConflictPenalty(
        array $candidate,
        array $studentIds,
        array $studentSchedules,
        int $totalStudents,
        EngineInput $input
    ): array {
        $conflictStudentIds = [];

        foreach ($studentSchedules as $schedule) {
            if ($schedule->day !== $candidate['day']->value) {
                continue;
            }

            if ($this->slotsOverlap(
                $candidate['start_slot'],
                $candidate['end_slot'],
                $schedule->startSlot,
                $schedule->endSlot
            )) {
                $studentsInSubject = $input->getStudentsForSubject($schedule->subjectId);
                foreach ($studentsInSubject as $studentId) {
                    if (in_array($studentId, $studentIds, true) && ! in_array($studentId, $conflictStudentIds, true)) {
                        $conflictStudentIds[] = $studentId;
                    }
                }
            }
        }

        $conflictCount = count($conflictStudentIds);

        if ($conflictCount === 0) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = (int) round($input->rules->penaltyStudentConflict * ($conflictCount / $totalStudents));

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(1, 'Mahasiswa bentrok', $penalty, [
                'count' => $conflictCount,
                'student_ids' => $conflictStudentIds,
            ]),
        ];
    }

    private function slotsOverlap(int $start1, int $end1, int $start2, int $end2): bool
    {
        return $start1 <= $end2 && $start2 <= $end1;
    }

    private function calculateLunchProximityPenalty(array $candidate): array
    {
        $lunchStartSlot = $this->findSlotByTime($this->rules->lunchBreakStart);
        $distance = abs($candidate['start_slot'] - $lunchStartSlot);

        if ($distance > $this->rules->proximityWindowSlots) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = (int) round($this->rules->penaltyLunchProximity * (1 - $distance / $this->rules->proximityWindowSlots));

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

        if ($minDistance > $this->rules->proximityWindowSlots || $nearbySubjectId === null) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = (int) round($this->rules->penaltyLecturerProximity * (1 - $minDistance / $this->rules->proximityWindowSlots));

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

        if ($startHour >= $this->rules->workStartHour + $this->rules->proximityWindowSlots) {
            return ['penalty' => 0, 'factor' => null];
        }

        if ($startHour < $this->rules->workStartHour) {
            // masuk jam 7 dapet ekstra penalti berat (misal base 15 + 25 = 40 poin)
            $penalty = $this->rules->penaltyEarlyMorning + $this->rules->earlyExtremeExtraPenalty;
            $label = 'Jam ekstrem terlalu pagi';
        } else {
            // masuk jam 8 dapet penalti normal (0-15 poin)
            $penalty = max(0, (int) round($this->rules->penaltyEarlyMorning * (1 - ($startHour - $this->rules->workStartHour) / $this->rules->proximityWindowSlots)));
            $label = 'Jam terlalu pagi';
        }

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(4, $label, $penalty, [
                'start_hour' => $startHour,
            ]),
        ];
    }

    private function calculateLateAfternoonPenalty(array $candidate): array
    {
        $endHour = (int) explode(':', $candidate['end_time'])[0];
        $endMinute = (int) explode(':', $candidate['end_time'])[1];

        if ($endHour < $this->rules->workEndHour - $this->rules->proximityWindowSlots) {
            return ['penalty' => 0, 'factor' => null];
        }

        $hoursAfterNormal = ($endHour + ($endMinute / 60)) - $this->rules->workEndHour;

        if ($hoursAfterNormal <= 0) {
            // jam 14:00 - 16:00 (normal, max 15 poin)
            $hoursBeforeEnd = max(0, $this->rules->workEndHour - ($endHour + ($endMinute / 60)));
            $penalty = max(0, (int) round($this->rules->penaltyLateAfternoon * (1 - $hoursBeforeEnd / $this->rules->proximityWindowSlots)));
            $label = 'Jam terlalu sore';
        } else {
            // lewat dari 16:00 (hajar kelipatan 10 poin tiap jam telat)
            $penalty = $this->rules->penaltyLateAfternoon + (int) round($hoursAfterNormal * $this->rules->lateExtremeHourlyPenalty);
            $label = 'Jam ekstrem malam';
        }

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(5, $label, $penalty, [
                'end_time' => $candidate['end_time'],
            ]),
        ];
    }

    private function calculateCapacityPenalty(array $candidate, int $totalStudents): array
    {
        $difference = $candidate['room_capacity'] - $totalStudents;

        $penalty = min(
            $this->rules->penaltyCapacity,
            (int) round($this->rules->penaltyCapacity * $difference / ($difference + $this->rules->capacityTargetGap)),
        );

        if ($penalty === 0) {
            return ['penalty' => 0, 'factor' => null];
        }

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
        if (! $candidate['has_jumping'] || count($candidate['jumped_slots']) === 0) {
            return ['penalty' => 0, 'factor' => null];
        }

        $penalty = $this->rules->penaltySlotJumping * count($candidate['jumped_slots']);

        return [
            'penalty' => $penalty,
            'factor' => new CodeFactor(7, 'Melewati jam istirahat', $penalty, [
                'jumped_slots' => $candidate['jumped_slots'],
                'count' => count($candidate['jumped_slots']),
            ]),
        ];
    }

    private function findSlotByTime(string $time): int
    {
        foreach ($this->timeSlots as $slot) {
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

    private function compareScoredOptions(array $left, array $right): int
    {
        return ($right['score'] <=> $left['score'])
            ?: ($left['capacity_gap'] <=> $right['capacity_gap'])
            ?: ($left['day']->value <=> $right['day']->value)
            ?: ($left['start_slot'] <=> $right['start_slot'])
            ?: strcmp((string) $left['room_id'], (string) $right['room_id']);
    }
}
