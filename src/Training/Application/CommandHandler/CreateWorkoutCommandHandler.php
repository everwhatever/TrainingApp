<?php

declare(strict_types=1);

namespace App\Training\Application\CommandHandler;

use App\Training\Application\Command\CreateWorkoutCommand;
use App\Training\Domain\Event\Workout\WorkoutCreatedEvent;
use App\Training\Domain\Model\Aggregate\WorkoutFactory;
use App\Training\Domain\Repository\WorkoutRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class CreateWorkoutCommandHandler
{
    public function __construct(
        private WorkoutFactory $workoutFactory,
        private WorkoutRepository $workoutRepository,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function __invoke(CreateWorkoutCommand $command): void
    {
        $userId = $command->userId;

        $workout = $this->workoutFactory->create(Uuid::fromString($userId));

        try {
            $this->workoutRepository->save($workout);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Failed to create workout: ' . $exception->getMessage());
        }

        $workoutCreatedEvent = new WorkoutCreatedEvent($workout->getId(), new \DateTimeImmutable());
        $this->eventDispatcher->dispatch($workoutCreatedEvent);
    }
}