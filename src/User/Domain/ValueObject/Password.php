<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

class Password
{
    public function __construct(private string $hashedPassword)
    {
    }

    public static function fromString(string $plainPassword): self
    {
        if (strlen($plainPassword) < 8 || !preg_match('/[A-Z]/', $plainPassword)) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long and must contain at least one uppercase and lowercase letter');
        }

        return new self(password_hash($plainPassword, PASSWORD_DEFAULT));
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedPassword);
    }
}