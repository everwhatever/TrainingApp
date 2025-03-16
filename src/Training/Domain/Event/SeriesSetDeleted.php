<?php

declare(strict_types=1);

namespace App\Training\Domain\Event;

class SeriesSetDeleted
{
    public function __construct(private string $seriesSetId) {}

    public function toArray(): array
    {
        return ['seriesSetId' => $this->seriesSetId];
    }
}
