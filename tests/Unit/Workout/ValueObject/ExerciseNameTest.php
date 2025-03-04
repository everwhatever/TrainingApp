<?php

declare(strict_types=1);

namespace App\Tests\Unit\Training\ValueObject;

use App\Training\Domain\ValueObject\ExerciseName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ExerciseNameTest extends TestCase
{
    public function testShouldCreateExerciseNameSuccessfully(): void
    {
        $exerciseName = new ExerciseName('Bench Press');

        $this->assertEquals('bench press', $exerciseName->getExerciseName());
    }

    public function testShouldThrowExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid name');

        new ExerciseName('');
    }

    public function testShouldConvertNameToLowercase(): void
    {
        $exerciseName = new ExerciseName('DEADLIFT');
        $this->assertEquals('deadlift', $exerciseName->getExerciseName());
    }
}
