<?php

declare(strict_types=1);

namespace App\Tests\Functional\Training\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreateWorkoutApiControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?KernelBrowser $client = null;
    private string $userId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient(); // Zmiana self:: na static::

        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager'); // Poprawiony dostęp do kontenera

        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('TRUNCATE TABLE workouts');
        $connection->executeStatement('TRUNCATE TABLE users');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');

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
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }

    public function testShouldCreateWorkoutSuccessfully(): void
    {
        $payload = [
            'userId' => $this->userId,
        ];

        $this->client->request(
            'POST',
            '/api/v1/training/workout',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(['status' => Response::HTTP_CREATED, 'message' => 'Workout created'], $responseData);
    }

    public function testShouldReturnBadRequestWhenUserIdIsMissing(): void
    {
        $payload = [];

        $this->client->request(
            'POST',
            '/api/v1/training/workout',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'], $responseData);
    }

    public function testShouldReturnBadRequestForInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/api/v1/training/workout',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'], $responseData);
    }
}
