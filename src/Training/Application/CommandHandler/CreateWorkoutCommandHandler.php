<?php

declare(strict_types=1);

namespace App\Training\Application\CommandHandler;

use App\Training\Application\Command\CreateWorkoutCommand;
use App\Training\Application\Service\WorkoutService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateWorkoutCommandHandler
{
    public function __construct(private WorkoutService $workoutService) {}

    public function __invoke(CreateWorkoutCommand $command): void
    {
        $this->workoutService->createWorkout($command->userId);
    }
}
