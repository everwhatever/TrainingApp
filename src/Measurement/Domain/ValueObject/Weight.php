<?php

declare(strict_types=1);

namespace App\Measurement\Domain\ValueObject;

readonly class Weight
{
    public function __construct(private float $value)
    {
        if ($value < 20 || $value > 600) {
            throw new \InvalidArgumentException('Weight must be between 20 and 600.');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }
}