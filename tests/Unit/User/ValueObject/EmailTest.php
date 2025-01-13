<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\ValueObject;

use App\User\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testValidEmail(): void
    {
        $email = new Email('test@test.com');

        $this->assertSame('test@test.com', $email->getEmail());
    }

    public function testInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email');

        new Email('test.com');
    }

    public function testEmailWithSpecialCharacters(): void
    {
        $email = new Email('user+name@example.com');

        $this->assertSame('user+name@example.com', $email->getEmail());
    }
}