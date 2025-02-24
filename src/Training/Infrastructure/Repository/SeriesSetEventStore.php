<?php

declare(strict_types=1);

namespace App\Training\Infrastructure\Repository;

use App\Training\Domain\Event\SeriesSetAdded;
use App\Training\Domain\Repository\SeriesSetEventStore as EventStoreInterface;
use PDO;

class SeriesSetEventStore implements EventStoreInterface
{
    public function __construct(private PDO $pdo) {}

    public function appendSeriesSetAddedEvent(SeriesSetAdded $event): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO event_store (aggregate_id, event_type, payload, occurred_at) 
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $event->getSeriesId(),
            'SeriesSetAdded',
            json_encode($event->toArray()),
            (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function getEventsBySeriesId(string $seriesId): array
    {
        // TODO: Implement getEventsBySeriesId() method.
    }

}
