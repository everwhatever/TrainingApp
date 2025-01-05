<?php

declare(strict_types=1);

namespace App\User\Application\Query;

readonly class GetOneUserQuery
{
    public function __construct(public array $findBy)
    {
    }
}