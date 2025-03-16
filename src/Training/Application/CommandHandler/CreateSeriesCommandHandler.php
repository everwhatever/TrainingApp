<?php

declare(strict_types=1);

namespace App\Training\Application\CommandHandler;

use App\Training\Application\Command\CreateSeriesCommand;
use App\Training\Domain\Model\Entity\Series;
use App\Training\Domain\Repository\WorkoutRepository;
use App\Training\Domain\ValueObject\ExerciseName;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class CreateSeriesCommandHandler
{
    public function __construct(private WorkoutRepository $workoutRepository)
    {
    }

    public function __invoke(CreateSeriesCommand $command): void
    {
        $workout = $this->workoutRepository->findOneById($command->workoutId);

        if (!$workout) {
            throw new \RuntimeException('Workout not found');
        }

        $series = Series::create(
            Uuid::v4()->toRfc4122(),
            $command->workoutId,
            new ExerciseName($command->exerciseName)
        );

        $workout->addSeries($series);

        $this->workoutRepository->save($workout);
    }
}
