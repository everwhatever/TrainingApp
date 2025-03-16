<?php

declare(strict_types=1);

namespace App\Tests\Functional\Training\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetOneWorkoutApiControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?KernelBrowser $client = null;
    private string $workoutId;
    private string $userId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('TRUNCATE TABLE workouts');
        $connection->executeStatement('TRUNCATE TABLE users');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');

        // Tworzenie użytkownika
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

        // Tworzenie treningu
        $this->workoutId = '26/02/25/18:49-52449';
        $this->entityManager->getConnection()->executeStatement(
            'INSERT INTO workouts (id, user_id, started_at) VALUES (:id, :user_id, :started_at)',
            [
                'id' => $this->workoutId,
                'user_id' => $this->userId,
                'started_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]
        );
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }

    public function testShouldReturnWorkoutSuccessfully(): void
    {
        $this->client->request('GET', '/api/v1/training/workout?id=' . urlencode($this->workoutId));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('ok', $responseData['status']);
        $this->assertSame($this->workoutId, $responseData['data']['id']);
    }

    public function testShouldReturnBadRequestWhenIdIsMissing(): void
    {
        $this->client->request('GET', '/api/v1/training/workout');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('Workout ID is required', $responseData['message']);
    }

    public function testShouldReturnNotFoundWhenWorkoutDoesNotExist(): void
    {
        $invalidWorkoutId = '01/01/30/12:00-99999';

        $this->client->request('GET', '/api/v1/training/workout?id=' . urlencode($invalidWorkoutId));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('Workout not found', $responseData['message']);
    }
}
