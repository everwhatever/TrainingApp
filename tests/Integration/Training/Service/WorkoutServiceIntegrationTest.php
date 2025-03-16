<?php

declare(strict_types=1);

namespace App\Tests\Integration\Training\Service;

use App\Training\Application\Service\WorkoutService;
use App\Training\Domain\Event\Workout\WorkoutCreatedEvent;
use App\Training\Domain\Event\Workout\WorkoutDeletedEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class WorkoutServiceIntegrationTest extends KernelTestCase
{
    private WorkoutService $workoutService;
    private InMemoryTransport $transport;
    private string $userId;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->workoutService = $container->get(WorkoutService::class);
        $transport = $container->get('messenger.transport.async');

        if (!$transport instanceof InMemoryTransport) {
            $this->markTestSkipped('This test requires in-memory transport.');
        }

        $this->transport = $transport;
        $this->transport->reset();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('TRUNCATE TABLE workouts');
        $connection->executeStatement('TRUNCATE TABLE users');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');

        $this->userId = Uuid::v4()->toRfc4122();
        $connection->executeStatement(
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

    public function testShouldDispatchWorkoutCreatedEventToTransport(): void
    {
        $workoutId = $this->workoutService->createWorkout($this->userId);

        $messages = $this->transport->getSent();

        $this->assertCount(1, $messages, 'Powinien zostać wysłany 1 event.');
        $this->assertInstanceOf(WorkoutCreatedEvent::class, $messages[0]->getMessage());
        $this->assertSame($workoutId, $messages[0]->getMessage()->getWorkoutId());

        $exists = (bool) $this->entityManager->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM workouts WHERE id = :id',
            ['id' => $workoutId]
        );
        $this->assertTrue($exists, 'Trening powinien być zapisany w bazie.');
    }

    public function testShouldDispatchWorkoutDeletedEventToTransport(): void
    {
        $workoutId = $this->workoutService->createWorkout($this->userId);
        $this->transport->reset();

        $this->workoutService->deleteWorkout($workoutId);

        $messages = $this->transport->getSent();

        $this->assertCount(1, $messages, 'Powinien zostać wysłany 1 event usunięcia.');
        $this->assertInstanceOf(WorkoutDeletedEvent::class, $messages[0]->getMessage());
        $this->assertSame($workoutId, $messages[0]->getMessage()->getWorkoutId());

        $exists = (bool) $this->entityManager->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM workouts WHERE id = :id',
            ['id' => $workoutId]
        );
        $this->assertFalse($exists, 'Trening powinien zostać usunięty z bazy.');
    }
}
