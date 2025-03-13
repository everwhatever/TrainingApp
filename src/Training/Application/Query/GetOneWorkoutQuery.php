<?php

declare(strict_types=1);

namespace App\Training\Application\Query;

class GetOneWorkoutQuery
{
    public function __construct(public string $workoutId)
    {
    }
}