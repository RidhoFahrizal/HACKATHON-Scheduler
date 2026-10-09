<?php

namespace App\Domain\Scheduling\DTO;

class EngineResult
{
    public readonly string $thinkingLog;

    public function __construct(
        public readonly bool $success,
        public readonly array $steps,
        public readonly array $options = [],
    ) {
        $this->thinkingLog = implode("\n\n", array_map(
            fn(ThinkingStep $step) => $step->toString(),
            $this->steps
        ));
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'thinking_log' => $this->thinkingLog,
            'steps' => array_map(fn(ThinkingStep $step) => $step->toArray(), $this->steps),
            'options' => array_map(fn(ScheduleOption $opt) => $opt->toArray(), $this->options),
        ];
    }
}
