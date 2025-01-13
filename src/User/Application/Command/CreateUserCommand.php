<?php

declare(strict_types=1);

namespace App\User\Application\Command;

readonly class CreateUserCommand
{
    public function __construct(
        public string $email,
        public string $plainPassword,
        public string $firstName,
        public string $lastName
    )
    {
    }
}