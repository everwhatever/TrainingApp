<?php

declare(strict_types=1);

namespace App\Training\Application\DTO;

use App\Training\Domain\Model\Aggregate\Workout;

class WorkoutDTO
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $startedAt,
        public ?string $completedAt,
        public array $series = []
    ) {}

    public static function fromWorkout(Workout $workout): self
    {
        return new self(
            $workout->getId(),
            $workout->getUserId()->toRfc4122(),
            $workout->getStartedAt()->format('Y-m-d H:i:s'),
            $workout->getCompletedAt()?->format('Y-m-d H:i:s'),
            array_map(fn ($series) => SeriesDTO::fromSeries($series), $workout->getSeries())
        );
    }
}
