<?php

declare(strict_types=1);

namespace App\Exercise\Application\QueryHandler;

use App\Exercise\Application\Query\GetAllExercisesQuery;
use App\Exercise\Domain\Repository\ExerciseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetAllExercisesQueryHandler
{
    public function __construct(private ExerciseRepository $repository) {}

    public function __invoke(GetAllExercisesQuery $query): array
    {
        $exercises = $this->repository->findAll();

        return array_map(fn ($exercise) => [
            'id' => $exercise->getId(),
            'name' => $exercise->getName(),
            'description' => $exercise->getDescription(),
            'video_url' => $exercise->getVideoUrl(),
            'image_url' => $exercise->getImageUrl(),
        ], $exercises);
    }
}
