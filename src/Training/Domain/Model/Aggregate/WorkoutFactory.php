<?php

declare(strict_types=1);

namespace App\Training\Domain\Model\Aggregate;

use App\Training\Domain\Service\IdGenerator;
use DateTimeImmutable;

class WorkoutFactory
{
    private IdGenerator $idGenerator;

    public function __construct(IdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function create(string $userId): Workout
    {
        $id = $this->idGenerator->generateWorkoutId();
        $startedAt = new DateTimeImmutable();

        return Workout::create($id, $userId, $startedAt);
    }
}
