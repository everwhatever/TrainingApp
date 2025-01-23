<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\User\Domain\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
        }

        return new SecurityUser($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SecurityUser) {
            throw new \InvalidArgumentException('Expected an instance of ' . SecurityUser::class);
        }

        $domainUser = $user->getDomainUser();
        $reloadedUser = $this->userRepository->findOneBy(['id' => $domainUser->getId()]);

        if (!$reloadedUser) {
            throw new UserNotFoundException(sprintf('User with ID "%s" not found.', $domainUser->getId()));
        }

        return new SecurityUser($reloadedUser);
    }

    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class;
    }
}
