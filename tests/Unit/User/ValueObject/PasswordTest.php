<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\ValueObject;

use App\User\Domain\ValueObject\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testPasswordFromStringCreatesValidPassword(): void
    {
        $plainPassword = 'Valid123';
        $password = Password::fromString($plainPassword);

        $this->assertNotNull($password);
        $this->assertNotEmpty($password->getHashedPassword());
        $this->assertTrue($password->verify($plainPassword));
    }

    public function testPasswordFromStringThrowsExceptionForShortPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters long and must contain at least one uppercase and lowercase letter');

        Password::fromString('short');
    }

    public function testPasswordFromStringThrowsExceptionForMissingUppercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters long and must contain at least one uppercase and lowercase letter');

        Password::fromString('nouppercase123');
    }

    public function testPasswordVerifyReturnsFalseForInvalidPassword(): void
    {
        $plainPassword = 'Valid123';
        $invalidPassword = 'WrongPass123';

        $password = Password::fromString($plainPassword);

        $this->assertFalse($password->verify($invalidPassword));
    }

    public function testGetHashedPasswordReturnsHash(): void
    {
        $plainPassword = 'Valid123';
        $password = Password::fromString($plainPassword);

        $hashedPassword = $password->getHashedPassword();

        $this->assertNotSame($plainPassword, $hashedPassword);
        $this->assertTrue(password_verify($plainPassword, $hashedPassword));
    }
}
