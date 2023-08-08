<?php
declare(strict_types=1);

namespace Game;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

readonly class DBConnection
{
    private Connection $connection;

    public function __construct(
        string $host,
        string $db,
        string $user,
        string $pass
    ) {
        $this->connection = DriverManager::getConnection([
            'dbname' => $db,
            'driver' => 'mysqli',
            'host' => $host,
            'user' => $user,
            'password' => $pass,
        ]);
    }

    public function fetchRow(string $query, array $params = []): array
    {
        $result = $this->connection->executeQuery($query, $params);

        return $result->fetchAssociative() ?: [];
    }

    public function fetchRows(string $query, array $params = []): iterable
    {
        $result = $this->connection->executeQuery($query, $params);

        return $result->iterateAssociative();
    }

    public function execute(string $query, array $params = []): void
    {
        $this->connection->executeQuery($query, $params);
    }

    public function transaction(callable $procedure): void
    {
        $this->connection->transactional(fn() => $procedure($this));
    }
}
