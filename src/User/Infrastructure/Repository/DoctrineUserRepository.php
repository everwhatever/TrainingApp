<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class DoctrineUserRepository implements UserRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function save(User $user): void
    {
        $query = 'INSERT INTO users (id, first_name, last_name, email, password) 
                  VALUES (:id, :first_name, :last_name, :email, :password)
                  ON DUPLICATE KEY UPDATE 
                  first_name = :first_name, last_name = :last_name, email = :email, password = :password';

        $this->connection->executeStatement($query, [
            'id' => $user->getId()->toString(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail()->getEmail(),
            'password' => $user->getPassword()->getHashedPassword(),
        ]);
    }

    public function delete(Uuid $userId): void
    {
        $query = 'DELETE FROM users WHERE id = :id';

        $rowsAffected = $this->connection->executeStatement($query, [
            'id' => $userId,
        ]);

        if ($rowsAffected === 0) {
            throw new NotFoundHttpException('User not found');
        }
    }

    public function findBy(array $params): array
    {
        $query = 'SELECT * FROM users WHERE ' . $this->buildWhereClause($params);
        $result = $this->connection->fetchAllAssociative($query, $params);

        return array_map(fn(array $row) => $this->hydrateUser($row), $result);
    }

    public function findOneBy(array $params): ?User
    {
        if (isset($params['id'])) {
            $params['id'] = Uuid::fromString($params['id']);
        }

        $query = 'SELECT * FROM users WHERE ' . $this->buildWhereClause($params) . ' LIMIT 1';
        $result = $this->connection->fetchAssociative($query, $params);

        return $result ? $this->hydrateUser($result) : null;
    }

    private function buildWhereClause(array $params): string
    {
        return implode(' AND ', array_map(fn(string $key) => "$key = :$key", array_keys($params)));
    }

    private function hydrateUser(array $data): User
    {
        return new User(
            Uuid::fromString($data['id']),
            new Email($data['email']),
            new Password($data['password']),
            $data['first_name'],
            $data['last_name']
        );
    }
}