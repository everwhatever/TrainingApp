<?php

declare(strict_types=1);

namespace App\Tests\Integration\Measurement\Repository;

use App\Measurement\Domain\Model\Measurement;
use App\Measurement\Domain\ValueObject\Circumference;
use App\Measurement\Domain\ValueObject\Height;
use App\Measurement\Domain\ValueObject\Weight;
use App\Measurement\Infrastructure\Entity\DoctrineMeasurementHistory;
use App\Measurement\Infrastructure\Repository\DoctrineMeasurementRepository;
use App\User\Infrastructure\Repository\DoctrineUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DoctrineMeasurementRepositoryTest extends KernelTestCase
{
    private ?DoctrineMeasurementRepository $repository;

    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->repository = $container->get('test.' . DoctrineMeasurementRepository::class);
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->rollback();
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }

    public function testSaveAndFindLatest(): void
    {
        $userId = Uuid::v4();
        $measurement = new Measurement(1, $userId, new \DateTimeImmutable('now'));
        $measurement->setHeight(new Height(180));
        $measurement->setWeight(new Weight(75.5));
        $measurement->setNeckCircumference(new Circumference(40));

        $this->repository->save($measurement);

        $latestMeasurement = $this->repository->findLatestForUserId($userId);
        $this->assertNotNull($latestMeasurement);
        $this->assertEquals(180, $latestMeasurement->getHeight()->getValue());
        $this->assertEquals(75.5, $latestMeasurement->getWeight()->getValue());
        $this->assertEquals(40, $latestMeasurement->getNeckCircumference()->getValue());
    }

    public function testSaveAndFindAllHistory(): void
    {
        $userId = Uuid::v4();

        // First measurement
        $measurement1 = new Measurement(1, $userId, new \DateTimeImmutable('-2 days'));
        $measurement1->setHeight(new Height(180));
        $measurement1->setWeight(new Weight(76));
        $this->repository->save($measurement1);

        // Second measurement
        $measurement2 = new Measurement(2, $userId, new \DateTimeImmutable('-1 day'));
        $measurement2->setHeight(new Height(180));
        $measurement2->setWeight(new Weight(75.5));
        $this->repository->save($measurement2);

        $history = $this->repository->findAllForUserId($userId);

        $this->assertCount(2, $history);
        $this->assertEquals(76, $history[0]->getWeight()->getValue());
        $this->assertEquals(75.5, $history[1]->getWeight()->getValue());
    }

    public function testDeleteMeasurement(): void
    {
        $userId = Uuid::v4();
        $measurement = new Measurement(1, $userId, new \DateTimeImmutable('now'));
        $measurement->setHeight(new Height(180));
        $measurement->setWeight(new Weight(75.5));

        $this->repository->save($measurement);

        $latestMeasurement = $this->repository->findLatestForUserId($userId);
        $this->assertNotNull($latestMeasurement);

        $this->repository->deleteById($latestMeasurement->getId());

        $deletedMeasurement = $this->repository->findById($latestMeasurement->getId());
        $this->assertNull($deletedMeasurement);

        $history = $this->repository->findAllForUserId($userId);
        $this->assertCount(1, $history);
    }

    public function testFindAllForUserIdWithinDateRange(): void
    {
        $userId = Uuid::v4();
        $startDate = new \DateTimeImmutable('2025-01-03');
        $endDate = new \DateTimeImmutable('2025-01-10');

        $this->addHistoryMeasurement(1,$userId, '2025-01-03');
        $this->addHistoryMeasurement(2,$userId, '2025-01-05');
        $this->addHistoryMeasurement(3,$userId, '2025-01-12');

        $results = $this->repository->findAllForUserIdWithinDateRange($userId, $startDate, $endDate);


        $this->assertCount(2, $results);
        $this->assertEquals('2025-01-03', $results[0]->getRecordedAt()->format('Y-m-d'));
        $this->assertEquals('2025-01-05', $results[1]->getRecordedAt()->format('Y-m-d'));
    }

    public function testFindByUserIdAndDate(): void
    {
        $userId = Uuid::v4();
        $date = new \DateTimeImmutable('2025-01-05');

        $this->addHistoryMeasurement(1,$userId, '2025-01-03');
        $this->addHistoryMeasurement(2,$userId, '2025-01-05');
        $this->addHistoryMeasurement(3,$userId, '2025-01-12');

        $result = $this->repository->findByUserIdAndDate($userId, $date);

        $this->assertNotNull($result);
        $this->assertEquals($date->format('Y-m-d'), $result->getRecordedAt()->format('Y-m-d'));
    }

    private function addHistoryMeasurement(int $id, Uuid $userId, string $recordedAt): void
    {
        $measurement = new Measurement($id, $userId, new \DateTimeImmutable($recordedAt));
        $this->repository->save($measurement);
    }
}
