<?php

declare(strict_types=1);

namespace App\Exercise\Domain\ValueObject;

final readonly class ExerciseDescription
{
    private ?string $description;

    public function __construct(?string $description)
    {
        if ($description !== null) {
            $description = trim($description);

            if (mb_strlen($description) > 2000) {
                throw new \InvalidArgumentException('Description cannot exceed 2000 characters.');
            }
        }

        $this->description = $description;
    }

    public function __toString(): string
    {
        return $this->description ?? '';
    }
}
