<?php

declare(strict_types=1);

namespace App\Training\Application\CommandHandler;

use App\Training\Application\Command\DeleteWorkoutCommand;
use App\Training\Application\Service\WorkoutService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteWorkoutCommandHandler
{
    public function __construct(private WorkoutService $workoutService)
    {
    }

    public function __invoke(DeleteWorkoutCommand $command): void
    {
        $this->workoutService->deleteWorkout($command->workoutId);
    }
}
