<?php

declare(strict_types=1);

namespace App\Training\Infrastructure\Repository;

use App\Training\Domain\Model\Entity\Series;
use App\Training\Domain\Repository\SeriesRepository;

class DoctrineSeriesRepository implements SeriesRepository
{
    public function save(Series $series): void
    {
        // TODO: Implement save() method.
    }

    public function findById(string $id): ?Series
    {
        // TODO: Implement findById() method.
    }

    public function findByWorkoutId(string $workoutId): array
    {
        // TODO: Implement findByWorkoutId() method.
    }

    public function delete(string $id): void
    {
        // TODO: Implement delete() method.
    }
}