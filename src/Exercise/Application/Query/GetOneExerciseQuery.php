<?php

declare(strict_types=1);

namespace App\Exercise\Application\Query;

readonly class GetOneExerciseQuery
{
    public function __construct(public string $exerciseId) {}
}