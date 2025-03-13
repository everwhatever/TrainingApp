<?php

declare(strict_types=1);

namespace App\Training\Application\Query;

use DateTimeImmutable;

class GetAllWorkoutQuery
{
    public function __construct(
        public string $userId,
        public ?DateTimeImmutable $startedAt = null,
        public ?DateTimeImmutable $completedAt = null,
        public ?int $limit = null
    )
    {
    }
}