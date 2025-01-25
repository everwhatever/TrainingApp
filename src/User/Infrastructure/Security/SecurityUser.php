<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\User\Domain\Model\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(private User $user)
    {
    }

    public function getPassword(): ?string
    {
        return $this->user->getPassword()->getHashedPassword();
    }

    public function getRoles(): array
    {
        return $this->user->getRoles();
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getEmail()->getEmail();
    }

    public function getDomainUser(): User
    {
        return $this->user;
    }
}