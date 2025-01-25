<?php

declare(strict_types=1);

namespace App\User\Domain\Event;

use App\User\Domain\ValueObject\Email;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

readonly class UserDeletedEvent
{
    public function __construct(public Uuid $uuid, public DateTimeImmutable $deletedAt)
    {
    }
}