<?php

declare(strict_types=1);

namespace App\Training\Domain\Model\Entity;

use App\Training\Domain\ValueObject\ExerciseName;

class Series
{
    private string $id;
    private string $workoutId;
    private ExerciseName $exerciseName;

    /** @var Set[] */
    private array $seriesSets = [];

    private function __construct(string $id, string $workoutId, ExerciseName $exerciseName)
    {
        $this->id = $id;
        $this->workoutId = $workoutId;
        $this->exerciseName = $exerciseName;
    }

    public static function create(string $id, string $workoutId, ExerciseName $exerciseName): self
    {
        return new self($id, $workoutId, $exerciseName);
    }

    public function addSeriesSet(Set $seriesSet): void
    {
        $this->seriesSets[] = $seriesSet;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getWorkoutId(): string
    {
        return $this->workoutId;
    }

    public function getExerciseName(): ExerciseName
    {
        return $this->exerciseName;
    }

    public function getSeriesSets(): array
    {
        return $this->seriesSets;
    }
}
