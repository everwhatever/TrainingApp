<?php

declare(strict_types=1);

namespace App\Training\Domain\Repository;

use App\Training\Domain\Model\Aggregate\Workout;

interface WorkoutRepository
{
    public function save(Workout $workout): void;
    public function findOneById(string $id): ?Workout;
    public function findAllByUserId(string $userId): array;
    public function findAllWithinDateRange(
        string $userId,
        \DateTimeImmutable $startedAt,
        ?\DateTimeImmutable $completedAt = null
    ): array;
}
