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
        $this->assertNoErrorHappened($result);
        $this->assertNewPlayerCreated(self::PLAYER_NAME);
    }

    public function testRegisterWithExistingNickname(): void
    {
        $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $result = $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        self::assertNotNull($result);
        self::assertEquals('Username is already taken', $result->message);
    }

    public function testLoginWhenAccountDoesNotExist(): void
    {
        $result = $this->game->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);

        self::assertInstanceOf(Error::class, $result);
        self::assertEquals('Invalid username or password', $result->message);
    }

    public function testLoginWithIncorrectPassword(): void
    {
        $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $result = $this->game->login(self::PLAYER_NAME, 'wrongpassword');

        self::assertInstanceOf(Error::class, $result);
        self::assertEquals('Invalid username or password', $result->message);
    }

    public function testLoginSuccess(): void
    {
        $this->game->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $result = $this->game->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $this->assertNoErrorHappened($result);

        $player = $this->game->getCurrentPlayer();
        self::assertNotNull($player);
        self::assertSame(self::PLAYER_NAME, $player->getName());
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

    private function assertNoErrorHappened(?Error $result): void
    {
        $message = '';
        if ($result !== null) {
            $message = $result->message;
        }

        self::assertNull($result, $message);
    }
}
