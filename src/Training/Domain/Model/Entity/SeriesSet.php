<?php

declare(strict_types=1);

namespace App\Training\Domain\Model\Entity;

use DateTimeImmutable;

class SeriesSet
{
    private string $id;
    private string $seriesId;
    private int $repetitions;
    private float $weight;
    private DateTimeImmutable $performedAt;

    private function __construct(string $id, string $seriesId, int $repetitions, float $weight, DateTimeImmutable $performedAt)
    {
        $this->id = $id;
        $this->seriesId = $seriesId;
        $this->repetitions = $repetitions;
        $this->weight = $weight;
        $this->performedAt = $performedAt;
    }

    public static function create(string $id, string $seriesId, int $repetitions, float $weight): self
    {
        return new self($id, $seriesId, $repetitions, $weight, new DateTimeImmutable());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSeriesId(): string
    {
        return $this->seriesId;
    }

    public function getRepetitions(): int
    {
        return $this->repetitions;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getPerformedAt(): DateTimeImmutable
    {
        return $this->performedAt;
    }
}
