<?php

declare(strict_types=1);

namespace App\User\Infrastructure\EventListener;

use App\User\Domain\Event\UserCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class SendWelcomeEmailListener
{
    public function __invoke(UserCreatedEvent $event): void
    {
        return;
    }
}