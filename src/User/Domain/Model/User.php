<?php

declare(strict_types=1);

namespace App\User\Domain\Model;

use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class User
{
    public function __construct(
        private readonly Uuid $uuid,
        private readonly string $email,
        private string $password,
        private readonly string $firstName,
        private readonly string $lastName
    )
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getEmail(): string
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

    public function changePassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getId(): Uuid
    {
        return $this->uuid;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}