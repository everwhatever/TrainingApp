<?php

declare(strict_types=1);

namespace App\Tests\Integration\User\Repository;

use App\User\Domain\Model\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Infrastructure\Repository\DoctrineUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DoctrineUserRepositoryTest extends KernelTestCase
{
    private ?DoctrineUserRepository $repository;

    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->repository = $container->get('test.' . DoctrineUserRepository::class);
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

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

    public function testSaveAndFindUser(): void
    {
        $user = new User(
            Uuid::v4(),
            new Email('test@example.com'),
            new Password('Password111'),
            'John',
            'Doe'
        );

        $this->repository->save($user);

        $foundUser = $this->repository->findOneBy(['email' => 'test@example.com']);

        $this->assertNotNull($foundUser);
        $this->assertSame('test@example.com', $foundUser->getEmail()->getEmail());
        $this->assertSame('John', $foundUser->getFirstName());
    }

    public function testDeleteUser(): void
    {
        $user = new User(
            Uuid::v4(),
            new Email('test@example.com'),
            new Password('Password111'),
            'John',
            'Doe'
        );

        $this->repository->save($user);
        $foundUser = $this->repository->findOneBy(['email' => 'test@example.com']);
        $this->assertNotNull($foundUser);

        $this->repository->delete($foundUser->getId());
        $foundUser = $this->repository->findOneBy(['email' => 'test@example.com']);
        $this->assertNull($foundUser);
    }
}