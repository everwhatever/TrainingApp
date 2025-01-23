<?php

declare(strict_types=1);

namespace App\Measurement\Domain\ValueObject;

readonly class Circumference
{
    public function __construct(private float $value)
    {
        if ($value < 0 || $value > 600) {
            throw new \InvalidArgumentException('Circumference must be between 0 and 600.');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }
}