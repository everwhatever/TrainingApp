<?php

declare(strict_types=1);

namespace App\Training\Application\Command;

readonly class DeleteWorkoutCommand
{
    public function __construct(public string $workoutId)
    {
    }
}
