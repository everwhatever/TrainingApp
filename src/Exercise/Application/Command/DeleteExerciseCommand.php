<?php

declare(strict_types=1);

namespace App\Exercise\Application\Command;

readonly class DeleteExerciseCommand
{
    public function __construct(public string $exerciseId) {}
}
