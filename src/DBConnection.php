<?php
declare(strict_types=1);

namespace Game;

/**
 * TODO has to be replaced with some vendor wrapper. Currently contains SQL-injections vulnerabilities
 */
readonly class DBConnection
{
    private \mysqli $connection;

    public function __construct(
        string $host,
        string $db,
        string $user,
        string $pass
    ) {
        $this->connection = new \mysqli($host, $user, $pass, $db);
    }

    public function fetchRow(string $query): array
    {
        $statement = $this->connection->query($query);

        return $statement->fetch_assoc() ?? [];
    }

    public function fetchRows(string $query): iterable
    {
        $statement = $this->connection->query($query);

        return $statement->getIterator();
    }

    public function execute(string $query): void
    {
        $this->connection->query($query);
    }
}