<?php

namespace App\Training\Domain\Repository;

use App\Training\Domain\Event\SeriesSetAdded;

interface SeriesSetEventStore
{
    public function appendSeriesSetAddedEvent(SeriesSetAdded $event): void;
    public function getEventsBySeriesId(string $seriesId): array;
}
