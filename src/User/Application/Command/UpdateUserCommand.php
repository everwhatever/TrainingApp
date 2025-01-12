<?php

declare(strict_types=1);

namespace App\User\Application\Command;

readonly class UpdateUserCommand
{
    public function __construct(
        public string $id,
        public ?string $firstName,
        public ?string $lastName
    )
    {
    }
}