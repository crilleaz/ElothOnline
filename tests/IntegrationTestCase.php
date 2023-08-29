<?php
declare(strict_types=1);

namespace Game;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Game\Engine\DBConnection;
use Game\Engine\DbTimeFactory;
use Game\Engine\Error;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected DBConnection $db;

    protected ?CarbonImmutable $currentTime = null;

    protected Game $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setCurrentTime(CarbonImmutable::now());
        $this->game = $this->getService(Game::class);
        $this->db = $this->getService(DBConnection::class);
        $this->db->startTransaction();
        $this->db->execute("UPDATE timetable SET tid = ?", [DbTimeFactory::createTimestamp($this->currentTime)]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->db = $this->getService(DBConnection::class);
        $this->db->rollbackTransaction();
        unset($_SESSION['username']);
        CarbonImmutable::setTestNow(null);
        Carbon::setTestNow(null);
        $this->currentTime = null;
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

    protected function setCurrentTime(DateTimeInterface $dateTime): void
    {
        $this->currentTime = CarbonImmutable::create($dateTime);

        Carbon::setTestNow($this->currentTime);
        CarbonImmutable::setTestNow($this->currentTime);
    }

    protected function assertNoErrorOccurred(?Error $result): void
    {
        $message = '';
        if ($result !== null) {
            $message = $result->message;
        }

        self::assertNull($result, $message);
    }

    protected static function assertErrorOccurred(?Error $result, string $expectedMessage): void
    {
        self::assertInstanceOf(Error::class, $result);
        self::assertEquals($expectedMessage, $result->message);
    }
}
