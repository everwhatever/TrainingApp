<?php

declare(strict_types=1);

namespace App\Tests\Integration\Training\Service;

use App\Training\Application\Service\WorkoutService;
use App\Training\Domain\Event\Workout\WorkoutCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Uid\Uuid;

class WorkoutServiceIntegrationTest extends KernelTestCase
{
    private WorkoutService $workoutService;
    private TransportInterface $transport;
    private string $userId;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->workoutService = $container->get(WorkoutService::class);
        $this->transport = $container->get('messenger.transport.async');

        if (!$this->transport instanceof InMemoryTransport) {
            $this->markTestSkipped('This test requires in-memory transport.');
        }

        if ($this->transport instanceof InMemoryTransport) {
            $this->transport->reset();
        }

        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

        $connection = $entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('TRUNCATE TABLE workouts');
        $connection->executeStatement('TRUNCATE TABLE users');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');

        $this->userId = Uuid::v4()->toRfc4122();
        $entityManager->getConnection()->executeStatement(
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

    public function testShouldDispatchEventToTransport(): void
    {
        $workoutId = $this->workoutService->createWorkout($this->userId);

        $messages = $this->transport->getSent();

        $this->assertCount(1, $messages);
        $this->assertInstanceOf(WorkoutCreatedEvent::class, $messages[0]->getMessage());
        $this->assertSame($workoutId, $messages[0]->getMessage()->getWorkoutId());
    }
}
