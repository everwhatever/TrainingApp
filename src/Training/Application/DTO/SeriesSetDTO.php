<?php

declare(strict_types=1);

namespace App\Training\Application\DTO;

use App\Training\Domain\Model\Entity\SeriesSet;

class SeriesSetDTO
{
    public function __construct(
        public int $repetitions,
        public float $weight,
        public string $performedAt
    ) {}

    public static function fromSeriesSet(SeriesSet $set): self
    {
        return new self(
            $set->getRepetitions(),
            $set->getWeight(),
            $set->getPerformedAt()->format('Y-m-d H:i:s')
        );
    }
}
