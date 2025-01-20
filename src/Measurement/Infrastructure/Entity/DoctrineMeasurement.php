<?php

declare(strict_types=1);

namespace App\Measurement\Infrastructure\Entity;

use App\Measurement\Domain\Model\Measurement;
use App\Measurement\Domain\ValueObject\Circumference;
use App\Measurement\Domain\ValueObject\Height;
use App\Measurement\Domain\ValueObject\Weight;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "measurements")]
class DoctrineMeasurement
{
    #[ORM\Id]
    #[ORM\Column(type: "integer", unique: true)]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(type: "uuid")]
    private Uuid $userId;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $recordedAt;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $height = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $neckCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $chestCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $waistCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $abdomenCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $hipCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $leftThighCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $rightThighCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $leftCalfCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $rightCalfCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $leftArmCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $rightArmCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $leftForearmCircumference = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $rightForearmCircumference = null;

    public function toDomain(): Measurement
    {
        $measurement = new Measurement($this->id, $this->userId, $this->recordedAt);

        if ($this->height !== null) {
            $measurement->setHeight(new Height($this->height));
        }
        if ($this->weight !== null) {
            $measurement->setWeight(new Weight($this->weight));
        }
        if ($this->neckCircumference !== null) {
            $measurement->setNeckCircumference(new Circumference($this->neckCircumference));
        }
        if ($this->chestCircumference !== null) {
            $measurement->setChestCircumference(new Circumference($this->chestCircumference));
        }
        if ($this->waistCircumference !== null) {
            $measurement->setWaistCircumference(new Circumference($this->waistCircumference));
        }
        if ($this->abdomenCircumference !== null) {
            $measurement->setAbdomenCircumference(new Circumference($this->abdomenCircumference));
        }
        if ($this->hipCircumference !== null) {
            $measurement->setHipCircumference(new Circumference($this->hipCircumference));
        }
        if ($this->leftThighCircumference !== null) {
            $measurement->setLeftThighCircumference(new Circumference($this->leftThighCircumference));
        }
        if ($this->rightThighCircumference !== null) {
            $measurement->setRightThighCircumference(new Circumference($this->rightThighCircumference));
        }
        if ($this->leftCalfCircumference !== null) {
            $measurement->setLeftCalfCircumference(new Circumference($this->leftCalfCircumference));
        }
        if ($this->rightCalfCircumference !== null) {
            $measurement->setRightCalfCircumference(new Circumference($this->rightCalfCircumference));
        }
        if ($this->leftArmCircumference !== null) {
            $measurement->setLeftArmCircumference(new Circumference($this->leftArmCircumference));
        }
        if ($this->rightArmCircumference !== null) {
            $measurement->setRightArmCircumference(new Circumference($this->rightArmCircumference));
        }
        if ($this->leftForearmCircumference !== null) {
            $measurement->setLeftForearmCircumference(new Circumference($this->leftForearmCircumference));
        }
        if ($this->rightForearmCircumference !== null) {
            $measurement->setRightForearmCircumference(new Circumference($this->rightForearmCircumference));
        }

        return $measurement;
    }

    public static function fromDomain(Measurement $measurement): self
    {
        $entity = new self();
        $entity->id = $measurement->getId();
        $entity->userId = $measurement->getUserId();
        $entity->recordedAt = $measurement->getRecordedAt();

        $entity->height = $measurement->getHeight()?->getValue();
        $entity->weight = $measurement->getWeight()?->getValue();
        $entity->neckCircumference = $measurement->getNeckCircumference()?->getValue();
        $entity->chestCircumference = $measurement->getChestCircumference()?->getValue();
        $entity->waistCircumference = $measurement->getWaistCircumference()?->getValue();
        $entity->abdomenCircumference = $measurement->getAbdomenCircumference()?->getValue();
        $entity->hipCircumference = $measurement->getHipCircumference()?->getValue();
        $entity->leftThighCircumference = $measurement->getLeftThighCircumference()?->getValue();
        $entity->rightThighCircumference = $measurement->getRightThighCircumference()?->getValue();
        $entity->leftCalfCircumference = $measurement->getLeftCalfCircumference()?->getValue();
        $entity->rightCalfCircumference = $measurement->getRightCalfCircumference()?->getValue();
        $entity->leftArmCircumference = $measurement->getLeftArmCircumference()?->getValue();
        $entity->rightArmCircumference = $measurement->getRightArmCircumference()?->getValue();
        $entity->leftForearmCircumference = $measurement->getLeftForearmCircumference()?->getValue();
        $entity->rightForearmCircumference = $measurement->getRightForearmCircumference()?->getValue();

        return $entity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getRecordedAt(): \DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getNeckCircumference(): ?float
    {
        return $this->neckCircumference;
    }

    public function getChestCircumference(): ?float
    {
        return $this->chestCircumference;
    }

    public function getWaistCircumference(): ?float
    {
        return $this->waistCircumference;
    }

    public function getAbdomenCircumference(): ?float
    {
        return $this->abdomenCircumference;
    }

    public function getHipCircumference(): ?float
    {
        return $this->hipCircumference;
    }

    public function getLeftThighCircumference(): ?float
    {
        return $this->leftThighCircumference;
    }

    public function getRightThighCircumference(): ?float
    {
        return $this->rightThighCircumference;
    }

    public function getLeftCalfCircumference(): ?float
    {
        return $this->leftCalfCircumference;
    }

    public function getRightCalfCircumference(): ?float
    {
        return $this->rightCalfCircumference;
    }

    public function getLeftArmCircumference(): ?float
    {
        return $this->leftArmCircumference;
    }

    public function getRightArmCircumference(): ?float
    {
        return $this->rightArmCircumference;
    }

    public function getLeftForearmCircumference(): ?float
    {
        return $this->leftForearmCircumference;
    }

    public function getRightForearmCircumference(): ?float
    {
        return $this->rightForearmCircumference;
    }
}
