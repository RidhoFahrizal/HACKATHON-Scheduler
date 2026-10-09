<?php

namespace App\Domain\Scheduling\DTO;

use App\Domain\Scheduling\Enums\Scope;

class RescheduleRequest
{
    public readonly int $targetWeek;
    public readonly array $weeksToEvaluate;

    public function __construct(
        public readonly int $scheduleId,
        public readonly string $scheduleName,
        public readonly Scope $scope,
        public readonly string $target,
    ) {
        $this->targetWeek = Scope::getWeeksFromDate($target);
        
        $this->weeksToEvaluate = match($scope) {
            Scope::ONCE => [$this->targetWeek],
            Scope::ONWARDS => Scope::getRemainingWeeks($this->targetWeek),
        };
    }
}
