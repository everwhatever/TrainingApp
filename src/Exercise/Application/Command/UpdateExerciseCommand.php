<?php

declare(strict_types=1);

namespace App\Exercise\Application\Command;

readonly class UpdateExerciseCommand
{
    public function __construct(
        public string  $exerciseId,
        public string  $name,
        public ?string $description,
        public ?string $videoUrl = null,
        public ?string $imageUrl = null
    ) {}
}
