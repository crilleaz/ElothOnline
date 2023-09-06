<?php

declare(strict_types=1);

namespace Game;

use Game\Dungeon\DungeonRepository;
use Game\Player\Activity\Activity;
use Game\Player\Activity\ActivityRepository;
use Game\Player\Player;

class ServerTest extends IntegrationTestCase
{
    private const MINUTES_SINCE_THE_LAST_EXECUTION = 67;

    private const PLAYER1 = 'Mark';
    private const PLAYER2 = 'Kyle';
    private const PLAYER3 = 'Gamid';
    private const PLAYER4 = 'Mila';
    private const PLAYER5 = 'Gi';

    private Server $server;
    protected function setUp(): void
    {
        parent::setUp();

        $this->server = $this->getService(Server::class);

        $dungeonRepository = $this->getService(DungeonRepository::class);
        $dungeon1          = $dungeonRepository->getById(1);
        $dungeon2          = $dungeonRepository->getById(2);

        $this->createCharacter(self::PLAYER1, 100)->enterDungeon($dungeon1);
        $this->createCharacter(self::PLAYER2, 40)->enterDungeon($dungeon2);
        $this->createCharacter(self::PLAYER3, 23);
        $this->createCharacter(self::PLAYER4, 61);
        $character = $this->createCharacter(self::PLAYER5, 65);

        $activity = Activity::lumberjack(1);
        $this->characterCanNowPerformActivity($character, $activity);
        $character->startActivity($activity);
    }

    public function testPerformTasksWhenThereIsNothingToDo(): void
    {
        $result = $this->server->performTasks();

        $this->assertStaminaRestoredForRestingPlayers(0);
        $this->assertStaminaSpentByHuntingCharacters(0);
        $this->assertStaminaSpentByCharactersPerformingActivities(0);

        $this->assertContainsLogs([], $result);
    }

    public function testPerformTasks(): void
    {
        $this->setCurrentTime($this->currentTime->addMinutes(self::MINUTES_SINCE_THE_LAST_EXECUTION));

        $logs = $this->server->performTasks();

        $this->assertStaminaRestoredForRestingPlayers(self::MINUTES_SINCE_THE_LAST_EXECUTION);
        $this->assertStaminaSpentByHuntingCharacters(self::MINUTES_SINCE_THE_LAST_EXECUTION);
        $this->assertStaminaSpentByCharactersPerformingActivities(self::MINUTES_SINCE_THE_LAST_EXECUTION);
        $this->assertHuntersReceivedRewards();
        $this->assertLabourersReceivedRewards();
        $this->assertExhaustedCharactersLeftTheDungeon();
        $this->assertExhaustedCharactersStoppedActivities();

        $this->assertContainsLogs(['<RestoredStamina>' . self::MINUTES_SINCE_THE_LAST_EXECUTION . '</RestoredStamina>'], $logs);
    }

    private function assertContainsLogs(array $logs, array $actualLogs): void
    {
        $expectedLogs = [
            sprintf('<StartedAt>%s</StartedAt>', $this->currentTime->format('H:i:s d-m-Y')),
            ... $logs,
        ];

        foreach ($expectedLogs as $log) {
            self::assertContains($log, $actualLogs);
        }
    }

    private function assertStaminaRestoredForRestingPlayers(int $amount): void
    {
        $player = $this->getCharacterByName(self::PLAYER3);
        self::assertEquals(
            min($amount + 23, Player::MAX_POSSIBLE_STAMINA),
            $player->getStamina()
        );

        $player = $this->getCharacterByName(self::PLAYER4);
        self::assertEquals(
            min($amount + 61, Player::MAX_POSSIBLE_STAMINA),
            $player->getStamina()
        );
    }

    private function assertStaminaSpentByHuntingCharacters(int $amountOfSpentStamina): void
    {
        $character = $this->getCharacterByName(self::PLAYER1);
        self::assertEquals(max(100 - $amountOfSpentStamina, 0), $character->getStamina());

        $character = $this->getCharacterByName(self::PLAYER2);
        self::assertEquals(max(40 - $amountOfSpentStamina, 0), $character->getStamina());
    }

    private function assertStaminaSpentByCharactersPerformingActivities(int $amountOfSpentStamina): void
    {
        $character = $this->getCharacterByName(self::PLAYER5);
        self::assertEquals(max(65 - $amountOfSpentStamina, 0), $character->getStamina());
    }

    /**
     * Drop calculations can not be performed because it contains rng(both for the item drop fact and the dropped quantity)
     */
    private function assertHuntersReceivedRewards(): void
    {
        $player = $this->getCharacterByName(self::PLAYER1);
        self::assertEquals(110, $player->getExp());

        $player = $this->getCharacterByName(self::PLAYER2);
        self::assertEquals(200, $player->getExp());

        $player = $this->getCharacterByName(self::PLAYER3);
        self::assertEquals(0, $player->getExp());

        $player = $this->getCharacterByName(self::PLAYER4);
        self::assertEquals(0, $player->getExp());
    }

    private function assertLabourersReceivedRewards(): void
    {
        $character    = $this->getCharacterByName(self::PLAYER5);
        $pickedOption = $this->getService(ActivityRepository::class)->findActivityOption(Activity::NAME_LUMBERJACK, 1);

        self::assertNotNull($pickedOption);
        self::assertEquals($pickedOption->rewardExp, $character->getExp());
        $item = $character->findInInventory($pickedOption->rewardItemId);
        self::assertNotNull($item);
        self::assertEquals(1, $item->quantity);
    }

    private function assertExhaustedCharactersLeftTheDungeon(): void
    {
        $character = $this->getCharacterByName(self::PLAYER1);
        self::assertTrue($character->isFighting());
        self::assertEquals(1, $character->getHuntingDungeonId(), 'Player had to stay in the same dungeon');

        $character = $this->getCharacterByName(self::PLAYER2);
        self::assertTrue($character->isInProtectiveZone());
    }

    private function assertExhaustedCharactersStoppedActivities(): void
    {
        $character = $this->getCharacterByName(self::PLAYER5);
        self::assertTrue($character->isInProtectiveZone());
        self::assertNull($character->getCurrentActivity());
    }
}
