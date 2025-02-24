<?php

declare(strict_types=1);

namespace App\Training\Domain\Event;

class SeriesSetUpdated
{
    public function __construct(
        private string $seriesSetId,
        private int $repetitions,
        private float $weight,
        private string $performedAt
    ) {}

    public function toArray(): array
    {
        return [
            'seriesSetId' => $this->seriesSetId,
            'repetitions' => $this->repetitions,
            'weight' => $this->weight,
            'performedAt' => $this->performedAt
        ];
    }
}
