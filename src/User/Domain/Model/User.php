<?php

declare(strict_types=1);

namespace App\User\Domain\Model;

use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class User
{
    public function __construct(
        private readonly Uuid   $uuid,
        private Email  $email,
        private Password        $password,
        private string $firstName,
        private string $lastName
    )
    {
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function getId(): Uuid
    {
        return $this->uuid;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function changePassword(Password $password): void
    {
        $this->password = $password;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}