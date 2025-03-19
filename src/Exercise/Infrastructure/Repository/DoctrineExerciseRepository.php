<?php

declare(strict_types=1);

namespace App\Exercise\Infrastructure\Repository;

use App\Exercise\Domain\Model\Exercise;
use App\Exercise\Domain\Repository\ExerciseRepository;
use Doctrine\DBAL\Connection;

readonly class DoctrineExerciseRepository implements ExerciseRepository
{
    public function __construct(private Connection $connection) {}

    public function save(Exercise $exercise): void
    {
        $this->connection->executeStatement(
            'REPLACE INTO exercises (id, name, description, video_url, image_url) VALUES (?, ?, ?, ?, ?)',
            [
                $exercise->getId(),
                $exercise->getName(),
                $exercise->getDescription(),
                $exercise->getVideoUrl(),
                $exercise->getImageUrl(),
            ]
        );
    }

    public function findById(string $id): ?Exercise
    {
        $result = $this->connection->fetchAssociative(
            'SELECT * FROM exercises WHERE id = ?',
            [$id]
        );

        return $result ? $this->hydrateExercise($result) : null;
    }

    public function findAll(): array
    {
        $results = $this->connection->fetchAllAssociative('SELECT * FROM exercises');

        return array_map(fn($row) => $this->hydrateExercise($row), $results);
    }

    public function delete(string $id): void
    {
        $this->connection->executeStatement('DELETE FROM exercises WHERE id = ?', [$id]);
    }

    private function hydrateExercise(array $data): Exercise
    {
        return Exercise::create(
            $data['id'],
            $data['name'],
            $data['description'] ?? null,
            $data['video_url'] ?? null,
            $data['image_url'] ?? null,
        );
    }

}
