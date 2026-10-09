<?php

namespace App\Domain\Scheduling\Actions;

use App\Domain\Scheduling\DTO\EngineInput;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\DTO\RoomDto;
use App\Domain\Scheduling\DTO\ScheduleDto;
use App\Domain\Scheduling\DTO\StudentSubjectDto;
use App\Domain\Scheduling\DTO\SubjectDto;
use App\Domain\Scheduling\Engine\SchedulingEngine;
use App\Models\Room;
use App\Models\Schedule;
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

        if (!$buildResult) {
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

        if (!$buildResult) {
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
            ->map(fn($r) => RoomDto::fromModel($r))
            ->toArray();

        $schedules = Schedule::all()
            ->map(fn($s) => ScheduleDto::fromModel($s))
            ->toArray();

        $subjects = Subject::all()
            ->map(fn($s) => SubjectDto::fromModel($s))
            ->toArray();

        $studentSubjects = StudentSubject::all()
            ->map(fn($ss) => StudentSubjectDto::fromModel($ss))
            ->toArray();

        return SchedulingEngine::buildInput(
            $scheduleId,
            $rooms,
            $schedules,
            $subjects,
            $studentSubjects
        );
    }
}
