<?php

declare(strict_types=1);

namespace App\Training\Domain\Event\Workout;

use DateTimeImmutable;

readonly class WorkoutCreatedEvent
{
    public function __construct(public string $workoutId, public DateTimeImmutable $createdAt)
    {
    }
}