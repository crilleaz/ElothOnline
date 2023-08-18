<?php
declare(strict_types=1);

namespace Game;

use Game\Engine\DBConnection;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected DBConnection $db;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db = $this->getService(DBConnection::class);
        $this->db->startTransaction();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->db = $this->getService(DBConnection::class);
        $this->db->rollbackTransaction();
        unset($_SESSION['username']);
    }

    /**
     * @template T
     * @param class-string<T> $className
     *
     * @return T
     */
    protected function getService(string $className): object
    {
        return \DI::getService($className);
    }
}
