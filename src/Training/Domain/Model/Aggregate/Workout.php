<?php

namespace App\Training\Domain\Model\Aggregate;

use App\Training\Domain\Model\Entity\Series;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class Workout
{
    private string $id;
    private Uuid $userId;
    private DateTimeImmutable $startedAt;
    private ?DateTimeImmutable $completedAt = null;

    /** @var Series[] */
    private array $series = [];

    private function __construct(string $id, Uuid $userId, DateTimeImmutable $startedAt)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->startedAt = $startedAt;
    }

    public static function create(string $id, Uuid $userId, DateTimeImmutable $startedAt): self
    {
        return new self($id, $userId, $startedAt);
    }

    public function addSeries(Series $series): void
    {
        $this->series[] = $series;
    }

    public function complete(): void
    {
        $this->completedAt = new DateTimeImmutable();
    }

    public function getSeries(): array
    {
        return $this->series;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }
}
