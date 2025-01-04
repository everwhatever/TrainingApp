<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Infrastructure\Entity\DoctrineUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineUserRepository implements UserRepository
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(DoctrineUser::class);
    }

    public function save(User $user): void
    {
        $doctrineUser = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($doctrineUser);
        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $doctrineUser = $this->repository->find($id);

        if ($doctrineUser !== null) {
            $this->entityManager->remove($doctrineUser);
            $this->entityManager->flush();
        }
    }

    public function findByEmail(string $email): ?User
    {
        $doctrineUser = $this->repository->findOneBy(['email' => $email]);
        return $doctrineUser?->toDomain();
    }

    public function findById(int $id): ?User
    {
        $doctrineUser = $this->repository->find($id);
        return $doctrineUser?->toDomain();
    }
}