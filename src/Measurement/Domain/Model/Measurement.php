<?php

declare(strict_types=1);

namespace App\Measurement\Domain\Model;

use App\Measurement\Domain\ValueObject\Height;
use App\Measurement\Domain\ValueObject\Weight;
use App\Measurement\Domain\ValueObject\Circumference;
use Symfony\Component\Uid\Uuid;

class Measurement
{
    private int $id;
    private ?Height $height = null;
    private ?Weight $weight = null;
    private ?Circumference $neckCircumference = null;
    private ?Circumference $chestCircumference = null;
    private ?Circumference $waistCircumference = null;
    private ?Circumference $abdomenCircumference = null;
    private ?Circumference $hipCircumference = null;
    private ?Circumference $leftThighCircumference = null;
    private ?Circumference $rightThighCircumference = null;
    private ?Circumference $leftCalfCircumference = null;
    private ?Circumference $rightCalfCircumference = null;
    private ?Circumference $leftArmCircumference = null;
    private ?Circumference $rightArmCircumference = null;
    private ?Circumference $leftForearmCircumference = null;
    private ?Circumference $rightForearmCircumference = null;

    private \DateTimeImmutable $recordedAt;

    private Uuid $userId;

    public function __construct(
        int $id,
        Uuid $userId,
        \DateTimeImmutable $recordedAt
    )
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->recordedAt = $recordedAt;
    }

    public function setHeight(Height $height): void
    {
        $this->height = $height;
    }

    public function setWeight(Weight $weight): void
    {
        $this->weight = $weight;
    }

    public function setNeckCircumference(Circumference $neckCircumference): void
    {
        $this->neckCircumference = $neckCircumference;
    }

    public function setChestCircumference(Circumference $chestCircumference): void
    {
        $this->chestCircumference = $chestCircumference;
    }

    public function setWaistCircumference(Circumference $waistCircumference): void
    {
        $this->waistCircumference = $waistCircumference;
    }

    public function setAbdomenCircumference(Circumference $abdomenCircumference): void
    {
        $this->abdomenCircumference = $abdomenCircumference;
    }

    public function setHipCircumference(Circumference $hipCircumference): void
    {
        $this->hipCircumference = $hipCircumference;
    }

    public function setLeftThighCircumference(Circumference $leftThighCircumference): void
    {
        $this->leftThighCircumference = $leftThighCircumference;
    }

    public function setRightThighCircumference(Circumference $rightThighCircumference): void
    {
        $this->rightThighCircumference = $rightThighCircumference;
    }

    public function setLeftCalfCircumference(Circumference $leftCalfCircumference): void
    {
        $this->leftCalfCircumference = $leftCalfCircumference;
    }

    public function setRightCalfCircumference(Circumference $rightCalfCircumference): void
    {
        $this->rightCalfCircumference = $rightCalfCircumference;
    }

    public function setLeftArmCircumference(Circumference $leftArmCircumference): void
    {
        $this->leftArmCircumference = $leftArmCircumference;
    }

    public function setRightArmCircumference(Circumference $rightArmCircumference): void
    {
        $this->rightArmCircumference = $rightArmCircumference;
    }

    public function setLeftForearmCircumference(Circumference $leftForearmCircumference): void
    {
        $this->leftForearmCircumference = $leftForearmCircumference;
    }

    public function setRightForearmCircumference(Circumference $rightForearmCircumference): void
    {
        $this->rightForearmCircumference = $rightForearmCircumference;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHeight(): ?Height
    {
        return $this->height;
    }

    public function getWeight(): ?Weight
    {
        return $this->weight;
    }

    public function getNeckCircumference(): ?Circumference
    {
        return $this->neckCircumference;
    }

    public function getChestCircumference(): ?Circumference
    {
        return $this->chestCircumference;
    }

    public function getWaistCircumference(): ?Circumference
    {
        return $this->waistCircumference;
    }

    public function getAbdomenCircumference(): ?Circumference
    {
        return $this->abdomenCircumference;
    }

    public function getHipCircumference(): ?Circumference
    {
        return $this->hipCircumference;
    }

    public function getLeftThighCircumference(): ?Circumference
    {
        return $this->leftThighCircumference;
    }

    public function getRightThighCircumference(): ?Circumference
    {
        return $this->rightThighCircumference;
    }

    public function getLeftCalfCircumference(): ?Circumference
    {
        return $this->leftCalfCircumference;
    }

    public function getRightCalfCircumference(): ?Circumference
    {
        return $this->rightCalfCircumference;
    }

    public function getLeftArmCircumference(): ?Circumference
    {
        return $this->leftArmCircumference;
    }

    public function getRightArmCircumference(): ?Circumference
    {
        return $this->rightArmCircumference;
    }

    public function getLeftForearmCircumference(): ?Circumference
    {
        return $this->leftForearmCircumference;
    }

    public function getRightForearmCircumference(): ?Circumference
    {
        return $this->rightForearmCircumference;
    }

    public function getRecordedAt(): \DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
