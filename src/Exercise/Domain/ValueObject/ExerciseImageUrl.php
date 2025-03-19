<?php

declare(strict_types=1);

namespace App\Exercise\Domain\ValueObject;

final readonly class ExerciseImageUrl
{
    private ?string $imageUrl;

    public function __construct(?string $imageUrl)
    {
        if ($imageUrl !== null) {
            $imageUrl = trim($imageUrl);

            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid image URL provided.');
            }
        }

        $this->imageUrl = $imageUrl;
    }

    public function __toString(): string
    {
        return $this->imageUrl ?? '';
    }

    public function isEmpty(): bool
    {
        return $this->imageUrl === null;
    }
}
