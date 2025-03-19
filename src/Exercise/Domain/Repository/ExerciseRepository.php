<?php

declare(strict_types=1);

namespace App\Exercise\Domain\Repository;

use App\Exercise\Domain\Model\Exercise;

interface ExerciseRepository
{
    public function save(Exercise $exercise): void;

    public function findById(string $id): ?Exercise;

    public function findAll(): array;

    public function delete(string $id): void;
}
