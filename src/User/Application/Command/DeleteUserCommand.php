<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use Symfony\Component\Uid\Uuid;

readonly class DeleteUserCommand
{
    public function __construct(public Uuid $userId)
    {
    }
}