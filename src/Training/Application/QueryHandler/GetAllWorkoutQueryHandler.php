<?php

declare(strict_types=1);

namespace App\Training\Application\QueryHandler;

use App\Training\Application\DTO\WorkoutDTO;
use App\Training\Application\Query\GetAllWorkoutQuery;
use App\Training\Domain\Repository\WorkoutRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetAllWorkoutQueryHandler
{
    public function __construct(private WorkoutRepository $workoutRepository)
    {
    }

    /**
     * @return WorkoutDTO[]
     */
    public function __invoke(GetAllWorkoutQuery $query): array
    {
        if ($query->startedAt !== null) {
            $workouts = $this->workoutRepository->findAllWithinDateRange(
                $query->userId,
                $query->startedAt,
                $query->completedAt
            );

            return array_map(
                fn($workout) => WorkoutDTO::fromWorkout($workout),
                $workouts
            );
        }

        $workouts = $this->workoutRepository->findAllByUserId($query->userId);

        return array_map(
            fn($workout) => WorkoutDTO::fromWorkout($workout),
            $workouts
        );
    }
}
