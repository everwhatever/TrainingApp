<?php

declare(strict_types=1);

namespace App\Tests\Unit\Training\Infrastructure\Service;

use App\Training\Infrastructure\Service\SystemIdGenerator;
use PHPUnit\Framework\TestCase;

class SystemIdGeneratorTest extends TestCase
{
    public function testShouldGenerateValidWorkoutId(): void
    {
        $idGenerator = new SystemIdGenerator();
        $workoutId = $idGenerator->generateWorkoutId();

        $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{2}\/\d{2}:\d{2}-\d{4,5}$/', $workoutId);

        $now = new \DateTimeImmutable();
        $datePart = $now->format('d/m/y/H:i');
        $this->assertStringStartsWith($datePart, $workoutId);
    }

    public function testShouldGenerateUniqueIds(): void
    {
        $idGenerator = new SystemIdGenerator();
        $workoutId1 = $idGenerator->generateWorkoutId();
        $workoutId2 = $idGenerator->generateWorkoutId();

        $this->assertNotEquals($workoutId1, $workoutId2);
    }
}
