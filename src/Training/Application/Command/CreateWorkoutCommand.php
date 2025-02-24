<?php

declare(strict_types=1);

namespace App\Training\Application\Command;

readonly class CreateWorkoutCommand
{
    public function __construct(
        public string $userId
    )
    {
    }
}