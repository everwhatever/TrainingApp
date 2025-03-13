<?php

declare(strict_types=1);

namespace App\Training\Application\QueryHandler;

use App\Training\Application\DTO\WorkoutDTO;
use App\Training\Application\Query\GetOneWorkoutQuery;
use App\Training\Domain\Repository\WorkoutRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetOneWorkoutQueryHandler
{
    public function __construct(private WorkoutRepository $workoutRepository)
    {
    }

    public function __invoke(GetOneWorkoutQuery $query): ?WorkoutDTO
    {
        $workout = $this->workoutRepository->findOneById($query->workoutId);

        if (!$workout) {
            return null;
        }

        return WorkoutDTO::fromWorkout($workout);
    }
}
