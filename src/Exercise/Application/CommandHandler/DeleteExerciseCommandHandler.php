<?php

declare(strict_types=1);

namespace App\Exercise\Application\CommandHandler;

use App\Exercise\Application\Command\DeleteExerciseCommand;
use App\Exercise\Domain\Repository\ExerciseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteExerciseCommandHandler
{
    public function __construct(private ExerciseRepository $repository) {}

    public function __invoke(DeleteExerciseCommand $command): void
    {
        $exercise = $this->repository->findOneBy(['id' => $command->exerciseId]);

        if (!$exercise) {
            throw new \RuntimeException('Exercise not found');
        }

        $this->repository->delete($command->exerciseId);
    }
}
