<?php

namespace App\Domain\Scheduling\DTO;

use App\Domain\Scheduling\Enums\Scope;

class RescheduleRequest
{
    public function __construct(
        public readonly int $scheduleId,
        public readonly Scope $scope,
    ) {}
}
