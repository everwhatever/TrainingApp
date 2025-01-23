<?php

namespace App\Measurement\Domain\Repository;

use App\Measurement\Domain\Model\Measurement;
use Symfony\Component\Uid\Uuid;

interface MeasurementRepository
{
    public function save(Measurement $measurement): void;

    public function findAllForUserId(Uuid $uuid): array;

    public function findById(int $measurementId): ?Measurement;

    public function findLatestForUserId(Uuid $uuid): ?Measurement;

    public function findByUserIdAndDate(Uuid $uuid, \DateTimeInterface $date): ?Measurement;

    public function deleteById(int $measurementId): void;

    public function findAllForUserIdWithinDateRange(Uuid $uuid, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array;
}