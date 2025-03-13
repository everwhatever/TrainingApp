<?php

declare(strict_types=1);

namespace App\Tests\Integration\Training\Repository;

use App\Training\Domain\Model\Aggregate\Workout;
use App\Training\Infrastructure\Repository\DoctrineWorkoutRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DoctrineWorkoutRepositoryTest extends KernelTestCase
{
    private ?DoctrineWorkoutRepository $repository;
    private ?EntityManagerInterface $entityManager;
    private string $userId;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->repository = $container->get('test.' . DoctrineWorkoutRepository::class);
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        $this->entityManager->beginTransaction();

        $this->userId = Uuid::v4()->toRfc4122();
        $this->entityManager->getConnection()->executeStatement(
            'INSERT INTO users (id, email, password, first_name, last_name) VALUES (:id, :email, :password, :first_name, :last_name)',
            [
                'id' => $this->userId,
                'email' => 'test@example.com',
                'password' => 'hashed_password',
                'first_name' => 'Test',
                'last_name' => 'User',
            ]
        );
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->rollback();
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }

    public function testShouldSaveAndRetrieveWorkout(): void
    {
        $workoutId = Uuid::v4();
        $startedAt = new DateTimeImmutable();

        $workout = Workout::create($workoutId->toRfc4122(), Uuid::fromString($this->userId), $startedAt);

        $this->repository->save($workout);

        $retrievedWorkout = $this->repository->findOneById($workoutId->toRfc4122());

        $this->assertNotNull($retrievedWorkout);
        $this->assertSame($workoutId->toRfc4122(), $retrievedWorkout->getId());
        $this->assertSame($this->userId, $retrievedWorkout->getUserId()->toRfc4122());
        $this->assertEquals(
            $startedAt->format('Y-m-d H:i:s'),
            $retrievedWorkout->getStartedAt()->format('Y-m-d H:i:s')
        );

    }

    public function testShouldReturnNullWhenWorkoutNotFound(): void
    {
        $nonExistentId = Uuid::v4()->toRfc4122();
        $retrievedWorkout = $this->repository->findOneById($nonExistentId);

        $this->assertNull($retrievedWorkout);
    }

    public function testShouldFindAllWorkoutsByUserId(): void
    {
        $workout1 = Workout::create(Uuid::v4()->toRfc4122(), Uuid::fromString($this->userId), new DateTimeImmutable('-1 day'));
        $workout2 = Workout::create(Uuid::v4()->toRfc4122(), Uuid::fromString($this->userId), new DateTimeImmutable());

        $this->repository->save($workout1);
        $this->repository->save($workout2);

        $retrievedWorkouts = $this->repository->findAllByUserId($this->userId);

        $this->assertCount(2, $retrievedWorkouts);
        $this->assertSame($this->userId, $retrievedWorkouts[0]->getUserId()->toRfc4122());
        $this->assertSame($this->userId, $retrievedWorkouts[1]->getUserId()->toRfc4122());
    }

    public function testShouldFindAllWorkoutsWithinDateRange(): void
    {
        $workout1 = Workout::create(Uuid::v4()->toRfc4122(), Uuid::fromString($this->userId), new DateTimeImmutable('-4 days'));
        $workout2 = Workout::create(Uuid::v4()->toRfc4122(), Uuid::fromString($this->userId), new DateTimeImmutable('-3 days'));
        $workout3 = Workout::create(Uuid::v4()->toRfc4122(), Uuid::fromString($this->userId), new DateTimeImmutable('-2 days'));
        $workout4 = Workout::create(Uuid::v4()->toRfc4122(), Uuid::fromString($this->userId), new DateTimeImmutable('-1 day'));

        $this->repository->save($workout1);
        $this->repository->save($workout2);
        $this->repository->save($workout3);
        $this->repository->save($workout4);

        // zakres od 3 dni temu do 1 dnia temu (powinien zwrócić 3 treningi)
        $startDate = new DateTimeImmutable('-3 days');
        $endDate = new DateTimeImmutable('-1 day');

        $retrievedWorkouts = $this->repository->findAllWithinDateRange($this->userId, $startDate, $endDate);

        $this->assertCount(3, $retrievedWorkouts);

        $retrievedWorkoutIds = array_map(fn(Workout $w) => $w->getId(), $retrievedWorkouts);

        $this->assertContains($workout2->getId(), $retrievedWorkoutIds);
        $this->assertContains($workout3->getId(), $retrievedWorkoutIds);
        $this->assertContains($workout4->getId(), $retrievedWorkoutIds);

        // zakres tylko z datą rozpoczęcia (powinien zwrócić wszystkie 4 treningi)
        $retrievedAllWorkouts = $this->repository->findAllWithinDateRange($this->userId, new DateTimeImmutable('-5 days'));
        $this->assertCount(4, $retrievedAllWorkouts);

        // zakres dat bez wyników
        $emptyResults = $this->repository->findAllWithinDateRange($this->userId, new DateTimeImmutable('-10 days'), new DateTimeImmutable('-9 days'));
        $this->assertCount(0, $emptyResults);
    }

}
