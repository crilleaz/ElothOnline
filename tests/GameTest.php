<?php
declare(strict_types=1);

namespace Game;

use Game\Player\LvlCalculator;
use Game\Player\Player;

class GameTest extends IntegrationTestCase
{
    private const PLAYER_NAME = 'MisterTester';
    private const PLAYER_PASSWORD = '123123123';

    public function testRegister(): void
    {
        $result = $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $this->assertNoErrorOccurred($result);
        $this->assertNewPlayerCreated(self::PLAYER_NAME);
    }

    public function testRegisterWithExistingNickname(): void
    {
        $firstAttempt = $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        self::assertNoErrorOccurred($firstAttempt);
        $secondAttempt = $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        self::assertErrorOccurred($secondAttempt, 'Username is already taken');
    }

    public function testLoginWhenAccountDoesNotExist(): void
    {
        $result = $this->game->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);

        self::assertErrorOccurred($result, 'Invalid username or password');
        $player = $this->game->getCurrentPlayer();
        self::assertNull($player);
    }

    public function testLoginWithIncorrectPassword(): void
    {
        $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $result = $this->game->login(self::PLAYER_NAME, 'wrongpassword');

        self::assertErrorOccurred($result, 'Invalid username or password');
    }

    public function testLoginSuccess(): void
    {
        $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $result = $this->game->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $this->assertNoErrorOccurred($result);

        $player = $this->game->getCurrentPlayer();
        self::assertNotNull($player);
        self::assertSame(self::PLAYER_NAME, $player->getName());
    }

    public function testLoginNotPossibleForBanned(): void
    {
        $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $this->game->banPlayer(self::PLAYER_NAME);

        $result = $this->game->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);

        self::assertErrorOccurred($result, 'User is banned');
        $player = $this->game->getCurrentPlayer();
        self::assertNull($player);
    }

    public function testListTopPlayers(): void
    {
        $lvls = [
            'MisterTester1' => LvlCalculator::minExpRequired(10),
            'MisterTester2' => LvlCalculator::minExpRequired(5),
            'MisterTester3' => LvlCalculator::minExpRequired(3),
            'MisterTester4' => LvlCalculator::minExpRequired(4),
            'MisterTester5' => LvlCalculator::minExpRequired(7),
            'MisterTester6' => LvlCalculator::minExpRequired(2),
            'MisterTester7' => LvlCalculator::minExpRequired(12),
            'MisterTester8' => LvlCalculator::minExpRequired(9),
            'MisterTester9' => LvlCalculator::minExpRequired(1),
            'MisterTester10' => LvlCalculator::minExpRequired(6),
        ];

        foreach($lvls as $newPlayerName => $exp) {
            $this->game->register($newPlayerName, self::PLAYER_PASSWORD);
            $player = $this->findPlayer($newPlayerName);
            self::assertNotNull($player);
            $player->addExp($exp);
        }

        $topPlayerNames = [];
        foreach ($this->game->listTopPlayers(7) as $topPlayer) {
            $topPlayerNames[] = $topPlayer->getName();
        }

        self::assertSame(
            [
                'MisterTester7',
                'MisterTester1',
                'MisterTester8',
                'MisterTester5',
                'MisterTester10',
                'MisterTester2',
                'MisterTester4',
            ],
            $topPlayerNames
        );
    }

    private function assertNewPlayerCreated(string $playerName): void
    {
        self::assertTrue(Player::exists($playerName, $this->db), 'Player does not exist');

        $player = Player::loadPlayer($playerName, $this->db);

        self::assertEquals($playerName, $player->getName());
        self::assertEquals(1, $player->getLevel());
        self::assertEquals(0, $player->getExp());
        self::assertEquals(100, $player->getStamina());
        self::assertEquals(15, $player->getCurrentHealth());
        self::assertEquals(15, $player->getMaxHealth());
        self::assertEquals(10, $player->getStrength());
        self::assertEquals(10, $player->getDefence());
        self::assertEquals(10, $player->getGold());
        self::assertFalse($player->isFighting());
        self::assertTrue($player->isInProtectiveZone());
        self::assertFalse($player->isAdmin());
        self::assertEquals(0, $player->getWoodcutting());
        self::assertEquals(0, $player->getHerbalism());
        self::assertEquals(0, $player->getHarvesting());
        self::assertEquals(0, $player->getBlacksmith());
        self::assertEquals(0, $player->getMining());
        self::assertEquals(0, $player->getGathering());
    }

    private function findPlayer(string $name): ?Player
    {
        if (Player::exists($name, $this->db)) {
            return Player::loadPlayer($name, $this->db);
        }

        return null;
    }
}
