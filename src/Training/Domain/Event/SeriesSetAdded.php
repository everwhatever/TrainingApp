<?php

declare(strict_types=1);

namespace App\Training\Domain\Event;

class SeriesSetAdded
{
    public function __construct(
        private string $seriesId,
        private int $repetitions,
        private float $weight,
        private string $performedAt
    ) {}

    public function toArray(): array
    {
        return [
            'seriesId' => $this->seriesId,
            'repetitions' => $this->repetitions,
            'weight' => $this->weight,
            'performedAt' => $this->performedAt
        ];
    }
}