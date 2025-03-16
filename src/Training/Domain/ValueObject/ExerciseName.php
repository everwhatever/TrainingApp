<?php

declare(strict_types=1);

namespace App\Training\Domain\ValueObject;

class ExerciseName
{
    public function __construct(private string $name)
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException('Invalid name');
        }

        $this->name = strtolower($this->name);
    }

    public function getExerciseName(): string
    {
        return $this->name;
    }
}