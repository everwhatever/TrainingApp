<?php

declare(strict_types=1);

namespace App\Training\Domain\Model\Aggregate;

use App\Training\Domain\Service\IdGenerator;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class WorkoutFactory
{
    private IdGenerator $idGenerator;

    public function __construct(IdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function create(Uuid $userId): Workout
    {
        $id = $this->idGenerator->generateWorkoutId();
        $startedAt = new DateTimeImmutable();

        return Workout::create($id, $userId, $startedAt);
    }
}
