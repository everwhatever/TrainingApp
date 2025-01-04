<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

class Email
{
    public function __construct(private string $email)
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }

        $this->email = strtolower($this->email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}