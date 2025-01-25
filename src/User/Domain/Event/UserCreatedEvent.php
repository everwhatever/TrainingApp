<?php

declare(strict_types=1);

namespace App\User\Domain\Event;

use App\User\Domain\ValueObject\Email;
use DateTimeImmutable;

readonly class UserCreatedEvent
{
    public function __construct(public Email $email, public DateTimeImmutable $createdAt)
    {
    }
}