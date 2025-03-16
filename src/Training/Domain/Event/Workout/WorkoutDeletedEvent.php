<?php

declare(strict_types=1);

namespace App\Training\Domain\Event\Workout;

readonly class WorkoutDeletedEvent
{
    public function __construct(
        private string $workoutId
    ) {
    }

    public function getWorkoutId(): string
    {
        return $this->workoutId;
    }
}
