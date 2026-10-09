<?php

namespace App\Domain\Scheduling\DTO;

class ThinkingStep
{
    public function __construct(
        public readonly int|string $stepNumber,
        public readonly string $title,
        public readonly string|int $thinkingCode,
        public readonly array $details = [],
    ) {}

    public function toArray(): array
    {
        return [
            'step' => $this->stepNumber,
            'title' => $this->title,
            'thinking_code' => $this->thinkingCode,
            'details' => $this->details,
        ];
    }

    public function toString(): string
    {
        $log = "[STEP {$this->stepNumber}: {$this->title}] (thinking code: {$this->thinkingCode})\n";
        foreach ($this->details as $key => $value) {
            $log .= '- ' . $key . ': ' . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
        return rtrim($log);
    }
}
