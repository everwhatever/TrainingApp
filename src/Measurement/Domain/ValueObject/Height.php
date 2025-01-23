<?php

declare(strict_types=1);

namespace App\Measurement\Domain\ValueObject;

readonly class Height
{
    public function __construct(private float $value)
    {
        if ($value < 50 || $value > 300) {
            throw new \InvalidArgumentException('Height must be between 50 and 300.');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }
}