<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetOneUserApiControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
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

    public function testGetUserByEmailSuccessfully(): void
    {
        $email = 'test@example.com';
        $this->mockUserInDatabase((int)Uuid::v4()->toBinary(), $email, 'John', 'Doe', 'Password123');

        $this->client->request('GET', '/api/v1/user', ['email' => $email]);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('ok', $responseData['status']);
        $this->assertSame($email, $responseData['data']['email']);
    }

    public function testGetUserNotFound(): void
    {
        $this->client->request('GET', '/api/v1/user', ['email' => 'notfound@example.com']);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('ok', $responseData['status']);
        $this->assertSame([], $responseData['data']);
    }

    public function testGetUserWithNoParameters(): void
    {
        $this->client->request('GET', '/api/v1/user', []);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $responseData['status']);
        $this->assertSame('Invalid JSON body', $responseData['message']);
    }

    private function mockUserInDatabase(int $id, string $email, string $firstName, string $lastName, string $password): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $connection = $entityManager->getConnection();

        try {
            $connection->beginTransaction();

            $connection->insert('users', [
                'id' => $id,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => $password
            ]);

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}