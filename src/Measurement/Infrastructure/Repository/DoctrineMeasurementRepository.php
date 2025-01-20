<?php

declare(strict_types=1);

namespace App\Measurement\Infrastructure\Repository;

use App\Measurement\Domain\Model\Measurement;
use App\Measurement\Domain\Repository\MeasurementRepository;
use App\Measurement\Infrastructure\Entity\DoctrineMeasurement;
use App\Measurement\Infrastructure\Entity\DoctrineMeasurementHistory;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class DoctrineMeasurementRepository implements MeasurementRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Measurement $measurement): void
    {
        $repository = $this->entityManager->getRepository(DoctrineMeasurement::class);

        $currentMeasurement = $repository->findOneBy(['userId' => $measurement->getUserId()]);

        if ($currentMeasurement) {
            $history = DoctrineMeasurementHistory::fromCurrentMeasurement($currentMeasurement, 'update');
            $this->entityManager->persist($history);

            $this->entityManager->remove($currentMeasurement);
        }

        $doctrineMeasurement = DoctrineMeasurement::fromDomain($measurement);

        $history = DoctrineMeasurementHistory::fromCurrentMeasurement($doctrineMeasurement, 'add');
        $this->entityManager->persist($history);

        $this->entityManager->persist($doctrineMeasurement);
        $this->entityManager->flush();
    }


    public function findAllForUserId(Uuid $uuid): array
    {
        $repository = $this->entityManager->getRepository(DoctrineMeasurementHistory::class);
        $measurements = $repository->findBy(['userId' => $uuid, 'changeType' => 'add'], ['recordedAt' => 'ASC']);

        return array_map(
            fn(DoctrineMeasurementHistory $measurement) => $measurement->toDomain(),
            $measurements
        );
    }

    public function findById(int $measurementId): ?Measurement
    {
        $repository = $this->entityManager->getRepository(DoctrineMeasurement::class);
        $doctrineMeasurement = $repository->find($measurementId);

        return $doctrineMeasurement?->toDomain();
    }

    public function findLatestForUserId(Uuid $uuid): ?Measurement
    {
        $repository = $this->entityManager->getRepository(DoctrineMeasurement::class);
        $doctrineMeasurement = $repository->findOneBy(['userId' => $uuid]);

        return $doctrineMeasurement?->toDomain();
    }

    public function findByUserIdAndDate(Uuid $uuid, \DateTimeInterface $date): ?Measurement
    {
        $repository = $this->entityManager->getRepository(DoctrineMeasurementHistory::class);
        $doctrineMeasurement = $repository->findOneBy([
            'userId' => $uuid,
            'recordedAt' => $date,
        ], ['recordedAt' => 'ASC']);

        return $doctrineMeasurement?->toDomain();
    }

    public function deleteById(int $measurementId): void
    {
        $repository = $this->entityManager->getRepository(DoctrineMeasurement::class);
        $doctrineMeasurement = $repository->find($measurementId);

        if ($doctrineMeasurement) {
            $history = DoctrineMeasurementHistory::fromCurrentMeasurement($doctrineMeasurement, 'delete');
            $this->entityManager->persist($history);

            $this->entityManager->remove($doctrineMeasurement);
            $this->entityManager->flush();
        }
    }

    public function findAllForUserIdWithinDateRange(Uuid $uuid, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $repository = $this->entityManager->getRepository(DoctrineMeasurementHistory::class);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('userId', $uuid))
            ->andWhere(
                Criteria::expr()->andX(
                    Criteria::expr()->gte('recordedAt', $startDate),
                    Criteria::expr()->lte('recordedAt', $endDate)
                )
            )
            ->andWhere(Criteria::expr()->eq('changeType', 'add'))
            ->orderBy(['recordedAt' => 'ASC']);

        $results = $repository->matching($criteria)->toArray();

        return array_map(
            fn(DoctrineMeasurementHistory $measurement) => $measurement->toDomain(),
            $results
        );
    }
}
