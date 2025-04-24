<?php

declare(strict_types=1);

namespace App\Exercise\Application\CommandHandler;

use App\Exercise\Application\Command\UpdateExerciseCommand;
use App\Exercise\Domain\Repository\ExerciseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UpdateExerciseCommandHandler
{
    public function __construct(private ExerciseRepository $repository) {}

    public function __invoke(UpdateExerciseCommand $command): void
    {
        $exercise = $this->repository->findOneBy(['id' =>$command->exerciseId]);

        if (!$exercise) {
            throw new \RuntimeException('Exercise not found');
        }

        $updatedExercise = $exercise->update(
            $command->name,
            $command->description,
            $command->videoUrl,
            $command->imageUrl
        );

        $this->repository->save($updatedExercise);
    }
}
