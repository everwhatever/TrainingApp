<?php

declare(strict_types=1);

namespace App\Training\Domain\Repository;

use App\Training\Domain\Model\Entity\Series;

interface SeriesRepository
{
    public function save(Series $series): void;

    public function findById(string $id): ?Series;

    public function findByWorkoutId(string $workoutId): array;

    public function delete(string $id): void;
}
