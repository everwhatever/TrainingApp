<?php

declare(strict_types=1);

namespace App\Exercise\Domain\ValueObject;

final readonly class ExerciseVideoUrl
{
    private ?string $videoUrl;

    public function __construct(?string $videoUrl)
    {
        if ($videoUrl !== null) {
            $videoUrl = trim($videoUrl);

            if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid video URL provided.');
            }
        }

        $this->videoUrl = $videoUrl;
    }

    public function __toString(): string
    {
        return $this->videoUrl ?? '';
    }

    public function isEmpty(): bool
    {
        return $this->videoUrl === null;
    }
}
