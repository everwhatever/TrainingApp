<?php

declare(strict_types=1);

namespace App\Tests\Unit\Measurement\ValueObject;

use App\Measurement\Domain\ValueObject\Circumference;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CircumferenceTest extends TestCase
{
    public function testValidCircumference(): void
    {
        $circumference = new Circumference(50.5);
        $this->assertSame(50.5, $circumference->getValue());
    }

    public function testMinimumBoundaryCircumference(): void
    {
        $circumference = new Circumference(0);
        $this->assertSame(0.0, $circumference->getValue());
    }

    public function testMaximumBoundaryCircumference(): void
    {
        $circumference = new Circumference(600);
        $this->assertSame(600.0, $circumference->getValue());
    }

    public function testCircumferenceBelowZeroThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Circumference must be between 0 and 600.');
        new Circumference(-1);
    }

    public function testCircumferenceAboveMaximumThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Circumference must be between 0 and 600.');
        new Circumference(601);
    }
}
