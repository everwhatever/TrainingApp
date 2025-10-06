<?php

declare(strict_types=1);

namespace App\Exercise\Domain\ValueObject;

final readonly class ExerciseName
{
    private string $name;

    public function __construct(string $name)
    {
        $trimmedName = trim($name);

        if (empty($trimmedName)) {
            throw new \InvalidArgumentException('Exercise name cannot be empty.');
        }

        if (mb_strlen($trimmedName) > 255) {
            throw new \InvalidArgumentException('Exercise name cannot exceed 255 characters.');
        }

        $this->name = $trimmedName;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
