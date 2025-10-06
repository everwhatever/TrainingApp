<?php

declare(strict_types=1);

namespace App\Exercise\Application\QueryHandler;

use App\Exercise\Application\Query\GetOneExerciseQuery;
use App\Exercise\Domain\Repository\ExerciseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetOneExerciseQueryHandler
{
    public function __construct(private ExerciseRepository $repository) {}

    public function __invoke(GetOneExerciseQuery $query): ?array
    {
        $exercise = $this->repository->findOneBy(['id' => $query->exerciseId]);

        return $exercise ? [
            'id' => $exercise->getId(),
            'name' => $exercise->getName(),
            'description' => $exercise->getDescription(),
            'video_url' => $exercise->getVideoUrl(),
            'image_url' => $exercise->getImageUrl(),
        ] : null;
    }
}
