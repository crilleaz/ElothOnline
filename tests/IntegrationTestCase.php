<?php

declare(strict_types=1);

namespace Game;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Game\Auth\AuthService;
use Game\Engine\DBConnection;
use Game\Engine\DbTimeFactory;
use Game\Engine\Error;
use Game\Player\CharacterRepository;
use Game\Player\Player;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected DBConnection $db;

    protected ?CarbonImmutable $currentTime = null;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setCurrentTime(CarbonImmutable::now());
        $this->authService = $this->getService(AuthService::class);
        $this->db          = $this->getService(DBConnection::class);
        $this->db->startTransaction();
        $this->db->execute('UPDATE timetable SET tid = ?', [DbTimeFactory::createTimestamp($this->currentTime)]);
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
     * @param    class-string<T> $className
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

    protected function getCharacterByName(string $name): Player
    {
        return $this->getService(CharacterRepository::class)->getByName($name);
    }

    protected function createCharacter(string $name, ?int $stamina = null): Player
    {
        $password = 'SomePassword';
        $this->authService->register($name, $password);
        $this->authService->login($name, $password);
        $user = $this->authService->getCurrentUser();

        $this->db->execute("INSERT INTO players(user_id, name, experience, health, health_max, defence, strength)
                            VALUE ($user->id, '$name', 0, 500, 50, 5, 10)");

        $character = $this->getService(CharacterRepository::class)->getByUser($user);

        if ($stamina !== null) {
            $this->setStamina($character, $stamina);
        }

        return $character;
    }

    private function setStamina(Player $character, int $value): void
    {
        $this->db->execute('UPDATE players SET stamina=? WHERE id = ?', [$value, $character->getId()]);
    }
}
