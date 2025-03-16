<?php

declare(strict_types=1);

namespace App\Training\Application\DTO;

use App\Training\Domain\Model\Entity\Series;

class SeriesDTO
{
    public function __construct(
        public string $exerciseName,
        public array $sets = []
    ) {}

    public static function fromSeries(Series $series): self
    {
        return new self(
            $series->getExerciseName()->getExerciseName(),
            array_map(fn ($set) => SeriesSetDTO::fromSeriesSet($set), $series->getSeriesSets())
        );
    }
}
