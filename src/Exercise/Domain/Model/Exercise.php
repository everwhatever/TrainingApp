<?php

declare(strict_types=1);

namespace App\Exercise\Domain\Model;

use App\Exercise\Domain\ValueObject\ExerciseDescription;
use App\Exercise\Domain\ValueObject\ExerciseImageUrl;
use App\Exercise\Domain\ValueObject\ExerciseName;
use App\Exercise\Domain\ValueObject\ExerciseVideoUrl;

class Exercise
{
    private string $id;
    private ExerciseName $name;
    private ExerciseDescription $description;
    private ExerciseVideoUrl $videoUrl;
    private ExerciseImageUrl $imageUrl;

    private function __construct(
        string $id,
        ExerciseName $name,
        ExerciseDescription $description,
        ExerciseVideoUrl $videoUrl,
        ExerciseImageUrl $imageUrl
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->videoUrl = $videoUrl;
        $this->imageUrl = $imageUrl;
    }

    public static function create(
        string $id,
        ExerciseName $name,
        ExerciseDescription $description,
        ExerciseVideoUrl $videoUrl,
        ExerciseImageUrl $imageUrl
    ): self
    {
        return new self($id, $name, $description, $videoUrl, $imageUrl);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ExerciseName
    {
        return $this->name;
    }

    public function getDescription(): ExerciseDescription
    {
        return $this->description;
    }

    public function getVideoUrl(): ExerciseVideoUrl
    {
        return $this->videoUrl;
    }

    public function getImageUrl(): ExerciseImageUrl
    {
        return $this->imageUrl;
    }
}
