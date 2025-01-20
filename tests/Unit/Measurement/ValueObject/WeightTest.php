<?php

declare(strict_types=1);

namespace App\Tests\Unit\Measurement\ValueObject;

use App\Measurement\Domain\ValueObject\Weight;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class WeightTest extends TestCase
{
    public function testValidWeight(): void
    {
        $weight = new Weight(50.5);
        $this->assertSame(50.5, $weight->getValue());
    }

    public function testMinimumBoundaryWeight(): void
    {
        $weight = new Weight(20.0);
        $this->assertSame(20.0, $weight->getValue());
    }

    public function testMaximumBoundaryWeight(): void
    {
        $weight = new Weight(600);
        $this->assertSame(600.0, $weight->getValue());
    }

    public function testWeightBelowZeroThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Weight must be between 20 and 600.');
        new Weight(-1);
    }

    public function testWeightAboveMaximumThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Weight must be between 20 and 600.');
        new Weight(601);
    }
}
