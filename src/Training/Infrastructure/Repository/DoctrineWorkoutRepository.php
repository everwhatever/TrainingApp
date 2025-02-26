<?php

declare(strict_types=1);

namespace App\Training\Infrastructure\Repository;

use App\Training\Domain\Model\Aggregate\Workout;
use App\Training\Domain\Repository\WorkoutRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineWorkoutRepository implements WorkoutRepository
{
    public function __construct(private Connection $connection) {}

    public function save(Workout $workout): void
    {
        $data = [
            'id' => $workout->getId(),
            'user_id' => $workout->getUserId()->toString(),
            'started_at' => $workout->getStartedAt()->format('Y-m-d H:i:s'),
            'completed_at' => $workout->getCompletedAt()?->format('Y-m-d H:i:s'),
        ];

        $this->connection->transactional(function () use ($data) {
            $this->connection->executeStatement(
                'INSERT INTO workouts (id, user_id, started_at, completed_at) 
                 VALUES (:id, :user_id, :started_at, :completed_at)
                 ON DUPLICATE KEY UPDATE 
                    completed_at = VALUES(completed_at)',
                $data
            );
        });
    }

    public function findOneById(string $id): ?Workout
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('workouts')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $row = $stmt->fetchAssociative();

        if (!$row) {
            return null;
        }

        return $this->hydrateWorkout($row);
    }

    public function findAllByUserId(string $userId): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('workouts')
            ->where('user_id = :user_id')
            ->setParameter('user_id', Uuid::fromString($userId)->toString())
            ->executeQuery();

        $results = $stmt->fetchAllAssociative();

        return array_map(fn(array $row) => $this->hydrateWorkout($row), $results);
    }

    private function hydrateWorkout(array $data): Workout
    {
        return Workout::create(
            $data['id'],
            Uuid::fromString($data['user_id']),
            new DateTimeImmutable($data['started_at'])
        );
    }
}
