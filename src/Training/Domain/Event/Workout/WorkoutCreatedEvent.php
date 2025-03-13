<?php

declare(strict_types=1);

namespace App\Training\Domain\Event\Workout;

use DateTimeImmutable;

readonly class WorkoutCreatedEvent
{
    public function __construct(private string $workoutId, private DateTimeImmutable $createdAt)
    {
    }

    public function getWorkoutId(): string
    {
        return $this->workoutId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}