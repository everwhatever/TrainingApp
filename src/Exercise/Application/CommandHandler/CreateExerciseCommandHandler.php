<?php

declare(strict_types=1);

namespace App\Exercise\Application\CommandHandler;

use App\Exercise\Application\Command\CreateExerciseCommand;
use App\Exercise\Domain\Model\Exercise;
use App\Exercise\Domain\Repository\ExerciseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateExerciseCommandHandler
{
    public function __construct(private ExerciseRepository $repository) {}

    public function __invoke(CreateExerciseCommand $command): void
    {
        $exercise = Exercise::create(
            $command->name,
            $command->description,
            $command->videoUrl,
            $command->imageUrl
        );

        $this->repository->save($exercise);
    }
}
