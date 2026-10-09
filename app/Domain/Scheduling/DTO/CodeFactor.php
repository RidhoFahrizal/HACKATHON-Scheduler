<?php

namespace App\Domain\Scheduling\DTO;

class CodeFactor
{
    public function __construct(
        public readonly int $code,
        public readonly string $label,
        public readonly int $penalty,
        public readonly array $details = [],
    ) {}

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'label' => $this->label,
            'penalty' => $this->penalty,
            'details' => $this->details,
        ];
    }
}
