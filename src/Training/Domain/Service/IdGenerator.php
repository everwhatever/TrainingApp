<?php

namespace App\Training\Domain\Service;

interface IdGenerator
{
    public function generateWorkoutId(): string;
}