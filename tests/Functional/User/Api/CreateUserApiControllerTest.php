<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateUserApiControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        // Wyczyść tabelę users dla MySQL
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('TRUNCATE TABLE users');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }

    public function testCreateUserSuccessfully(): void
    {
        $payload = [
            'email' => 'test@test.com',
            'password' => 'Password111',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        $this->client->request(
            'POST',
            '/api/v1/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(['status' => 201, 'message' => 'User created'], $responseData);
    }

    public function testCreateUserWithInvalidEmail(): void
    {
        $payload = [
            'email' => 'invalid-email',
            'password' => 'Password123',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];

        $this->client->request(
            'POST',
            '/api/v1/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(['status' => 500, 'message' => 'Handling "App\User\Application\Command\CreateUserCommand" failed: Invalid email'], $responseData);
    }

    public function testCreateUserWithEmptyPayload(): void
    {
        $this->client->request(
            'POST',
            '/api/v1/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(['status' => 400, 'message' => 'Invalid JSON body'], $responseData);
    }
}
