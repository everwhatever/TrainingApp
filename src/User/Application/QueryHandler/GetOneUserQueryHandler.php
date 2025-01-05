<?php

declare(strict_types=1);

namespace App\User\Application\QueryHandler;

use App\User\Application\Query\GetOneUserQuery;
use App\User\Domain\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetOneUserQueryHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(GetOneUserQuery $query): array
    {
        $user = $this->userRepository->findOneBy($query->findBy);

        if (!$user) {
            return [];
        }

        return[
            'id' => $user->getId()->toRfc4122(),
            'email' => $user->getEmail()->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ];
    }
}