<?php

declare(strict_types=1);

namespace App\Tests\Unit\Measurement\ValueObject;

use App\Measurement\Domain\ValueObject\Height;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HeightTest extends TestCase
{
    public function testValidHeight(): void
    {
        $height = new Height(50.5);
        $this->assertSame(50.5, $height->getValue());
    }

    public function testMinimumBoundaryHeight(): void
    {
        $height = new Height(50);
        $this->assertSame(50.0, $height->getValue());
    }

    public function testMaximumBoundaryHeight(): void
    {
        $height = new Height(300.0);
        $this->assertSame(300.0, $height->getValue());
    }

    public function testHeightBelowZeroThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Height must be between 50 and 300.');
        new Height(-1);
    }

    public function testHeightAboveMaximumThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Height must be between 50 and 300.');
        new Height(601);
    }
}
