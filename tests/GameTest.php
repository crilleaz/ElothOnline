<?php
declare(strict_types=1);

namespace Game;

use Game\Engine\Error;
use Game\Player\Player;

class GameTest extends IntegrationTestCase
{
    private const PLAYER_NAME = 'MisterTester';
    private const PLAYER_PASSWORD = '123123123';

    private Game $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->game = $this->getService(Game::class);
    }

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

    private function assertNewPlayerCreated(string $playerName): void
    {
        self::assertTrue(Player::exists($playerName, $this->db), 'Player does not exist');

        $player = Player::loadPlayer($playerName, $this->db);

        self::assertEquals($playerName, $player->getName());
        self::assertEquals(1, $player->getLevel());
        self::assertEquals(100, $player->getStamina());
        self::assertEquals(15, $player->getCurrentHealth());
        self::assertEquals(15, $player->getMaxHealth());
        self::assertEquals(1, $player->getLevel());
        self::assertEquals(1, $player->getLevel());
        self::assertEquals(1, $player->getLevel());
        self::assertEquals(10, $player->getGold());
    }

    private function assertNoErrorOccurred(?Error $result): void
    {
        $message = '';
        if ($result !== null) {
            $message = $result->message;
        }

        self::assertNull($result, $message);
    }

    private static function assertErrorOccurred(?Error $result, string $expectedMessage): void
    {
        self::assertInstanceOf(Error::class, $result);
        self::assertEquals($expectedMessage, $result->message);
    }
}
