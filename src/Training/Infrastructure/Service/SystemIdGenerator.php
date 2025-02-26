<?php

declare(strict_types=1);

namespace App\Training\Infrastructure\Service;

use App\Training\Domain\Service\IdGenerator;
use Random\RandomException;

class SystemIdGenerator implements IdGenerator
{
    /**
     * @throws RandomException
     */
    public function generateWorkoutId(): string
    {
        $now = new \DateTimeImmutable();
        $datePart = $now->format('d/m/y/H:i'); // "dd/mm/rr/hh/mm"
        $randomInt = random_int(1000, 99999);

        return sprintf('%s-%d', $datePart, $randomInt);
    }
}
