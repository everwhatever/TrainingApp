<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Infrastructure\Entity\DoctrineUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Uid\Uuid;

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

    public function findById(Uuid $id): ?User
    {
        $doctrineUser = $this->repository->find($id->toRfc4122());
        return $doctrineUser?->toDomain();
    }

    public function findBy(array $params): array
    {
        $doctrineUsers = $this->repository->findBy($params);

        $users = [];
        foreach ($doctrineUsers as $doctrineUser) {
            $users[] = $doctrineUser->toDomain();
        }

        return $users;
    }

    public function findOneBy(array $params): ?User
    {
        $doctrineUser = $this->repository->findOneBy($params);
        return $doctrineUser?->toDomain();
    }
}