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
        $this->connection->transactional(function () use ($workout) {
            $workoutData = [
                'id' => $workout->getId(),
                'user_id' => $workout->getUserId()->toString(),
                'started_at' => $workout->getStartedAt()->format('Y-m-d H:i:s'),
                'completed_at' => $workout->getCompletedAt()?->format('Y-m-d H:i:s'),
            ];

            // Zapis Workout
            $this->connection->executeStatement(
                'INSERT INTO workouts (id, user_id, started_at, completed_at) 
             VALUES (:id, :user_id, :started_at, :completed_at)
             ON DUPLICATE KEY UPDATE 
                completed_at = VALUES(completed_at)',
                $workoutData
            );

            $seriesList = $workout->getSeries();

            if (!empty($seriesList)) {
                foreach ($seriesList as $series) {
                    $seriesData = [
                        'id' => $series->getId(),
                        'workout_id' => $workout->getId(),
                        'exercise_name' => $series->getExerciseName()->getExerciseName(),
                    ];

                    $this->connection->executeStatement(
                        'INSERT INTO series (id, workout_id, exercise_name) 
                         VALUES (:id, :workout_id, :exercise_name)
                         ON DUPLICATE KEY UPDATE
                            exercise_name = VALUES(exercise_name)',
                        $seriesData
                    );
                }
            }
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
        $qb = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('workouts')
            ->where('user_id = :user_id')
            ->setParameter('user_id', Uuid::fromString($userId)->toString());

        $results = $qb->executeQuery()->fetchAllAssociative();

        return array_map(fn(array $row) => $this->hydrateWorkout($row), $results);
    }

    public function findAllWithinDateRange(
        string $userId,
        \DateTimeImmutable $startedAt,
        ?\DateTimeImmutable $completedAt = null
    ): array {
        $qb = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('workouts')
            ->where('user_id = :user_id')
            ->andWhere('started_at >= :started_at')
            ->setParameter('user_id', Uuid::fromString($userId)->toString())
            ->setParameter('started_at', $startedAt->format('Y-m-d H:i:s'));

        if ($completedAt !== null) {
            $qb->andWhere('started_at <= :completed_at')
                ->setParameter('completed_at', $completedAt->format('Y-m-d H:i:s'));
        }

        $results = $qb->executeQuery()->fetchAllAssociative();

        return array_map(fn(array $row) => $this->hydrateWorkout($row), $results);
    }

    public function delete(string $workoutId): void
    {
        $this->connection->createQueryBuilder()
            ->delete('workouts')
            ->where('id = :id')
            ->setParameter('id', $workoutId)
            ->executeQuery();
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
