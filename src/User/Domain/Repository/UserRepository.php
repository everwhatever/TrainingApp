<?php

declare(strict_types=1);

namespace App\User\Domain\Repository;

use App\User\Domain\Model\User;
use Symfony\Component\Uid\Uuid;

interface UserRepository
{
    public function save(User $user): void;

    public function findBy(array $params): array;

    public function findOneBy(array $params): ?User;

    public function findById(Uuid $id): ?User;
}