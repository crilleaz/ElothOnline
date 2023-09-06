<?php

declare(strict_types=1);

namespace Game\Engine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\ParameterType;

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
            'dbname'   => $db,
            'driver'   => 'pdo_mysql',
            'host'     => $host,
            'user'     => $user,
            'password' => $pass,
        ]);
    }

    public function fetchRow(string $query, array $params = []): array
    {
        $result = $this->connection->executeQuery($query, $params, $this->detectTypes($params));

        return $result->fetchAssociative() ?: [];
    }

    public function fetchRows(string $query, array $params = []): iterable
    {
        $result = $this->connection->executeQuery($query, $params, $this->detectTypes($params));

        return $result->iterateAssociative();
    }

    public function execute(string $query, array $params = []): void
    {
        $this->connection->executeQuery($query, $params, $this->detectTypes($params));
    }

    public function transaction(callable $procedure): void
    {
        $this->connection->transactional(fn() => $procedure($this));
    }

    /**
     * @internal only for internal usages
     */
    public function startTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * @internal only for internal usages
     */
    public function rollbackTransaction(): void
    {
        $this->connection->rollBack();
    }

    private function detectTypes(array $params): array
    {
        $types = [];
        foreach ($params as $key => $param) {
            switch (true) {
                case is_int($param):
                    $type = ParameterType::INTEGER;
                    break;
                case is_bool($param):
                    $type = ParameterType::BOOLEAN;
                    break;
                default:
                    $type = ParameterType::STRING;
                    break;
            }

            $types[$key] = $type;
        }

        return $types;
    }
}
