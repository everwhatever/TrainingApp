<?php

declare(strict_types=1);

namespace App\Training\Application\Command;

readonly class CreateSeriesCommand
{
    public function __construct(
        public string $workoutId,
        public string $exerciseName
    ) {}
}
