<?php

declare(strict_types=1);

namespace App\Training\Application\Service;

use App\Training\Domain\Event\Workout\WorkoutCreatedEvent;
use App\Training\Domain\Event\Workout\WorkoutDeletedEvent;
use App\Training\Domain\Model\Aggregate\WorkoutFactory;
use App\Training\Domain\Repository\WorkoutRepository;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

readonly class WorkoutService
{
    public function __construct(
        private WorkoutFactory      $workoutFactory,
        private WorkoutRepository   $workoutRepository,
        private MessageBusInterface $eventBus
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function createWorkout(string $userId): string
    {
        $workout = $this->workoutFactory->create(Uuid::fromString($userId));

        try {
            $this->workoutRepository->save($workout);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Failed to create workout: ' . $exception->getMessage());
        }

        $workoutCreatedEvent = new WorkoutCreatedEvent($workout->getId(), new \DateTimeImmutable());
        $this->eventBus->dispatch($workoutCreatedEvent);

        return $workoutCreatedEvent->getWorkoutId();
    }

    public function deleteWorkout(string $workoutId): void
    {
        $this->workoutRepository->delete($workoutId);

        $event = new WorkoutDeletedEvent($workoutId);
        $this->eventBus->dispatch($event);
    }
}
