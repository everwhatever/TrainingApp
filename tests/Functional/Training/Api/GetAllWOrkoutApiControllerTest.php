<?php

declare(strict_types=1);

namespace App\Tests\Functional\Training\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetAllWorkoutApiControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?KernelBrowser $client = null;
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

        // Tworzenie kilku treningów
        for ($i = 0; $i < 3; $i++) {
            $this->entityManager->getConnection()->executeStatement(
                'INSERT INTO workouts (id, user_id, started_at) VALUES (:id, :user_id, :started_at)',
                [
                    'id' => '26/02/25/18:4' . $i . '-5244' . $i,
                    'user_id' => $this->userId,
                    'started_at' => (new \DateTimeImmutable('-' . $i . ' days'))->format('Y-m-d H:i:s'),
                ]
            );
        }
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }

    public function testShouldReturnAllWorkoutsSuccessfully(): void
    {
        $this->client->request('GET', '/api/v1/training/workout-all?userId=' . urlencode($this->userId));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('ok', $responseData['status']);
        $this->assertCount(3, $responseData['data']);
    }

    public function testShouldReturnBadRequestWhenUserIdIsMissing(): void
    {
        $this->client->request('GET', '/api/v1/training/workout-all');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('User id is required', $responseData['message']);
    }

    public function testShouldReturnNotFoundWhenNoWorkoutsExist(): void
    {
        $unknownUserId = Uuid::v4()->toRfc4122();

        $this->client->request('GET', '/api/v1/training/workout-all?userId=' . urlencode($unknownUserId));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('Workout not found', $responseData['message']);
    }
}
