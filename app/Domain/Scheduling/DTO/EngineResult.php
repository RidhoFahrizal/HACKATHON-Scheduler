<?php

namespace App\Domain\Scheduling\DTO;

class EngineResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $thinkingLog,
        public readonly array $options = [],
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'thinking_log' => $this->thinkingLog,
            'options' => array_map(fn(ScheduleOption $opt) => $opt->toArray(), $this->options),
        ];
    }
}
