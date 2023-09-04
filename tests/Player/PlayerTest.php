<?php

declare(strict_types=1);

namespace Game\Player;

use Game\Dungeon\DungeonRepository;
use Game\IntegrationTestCase;
use Game\Item\Item;
use Game\Item\ItemPrototypeRepository;
use Game\Player\Activity\Activity;
use PHPUnit\Framework\Attributes\DataProvider;

class PlayerTest extends IntegrationTestCase
{
    private Player $player;

    private DungeonRepository $dungeonRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->player            = $this->createCharacter('Mick');
        $this->dungeonRepository = $this->getService(DungeonRepository::class);
    }

    public function testExpGain(): void
    {
        $this->player->addExp(100);
        self::assertEquals(100, $this->player->getExp());
        self::assertEquals(2, $this->player->getLevel());

        $this->player->addExp(431);
        self::assertEquals(100 + 431, $this->player->getExp());
        self::assertEquals(4, $this->player->getLevel());
    }

    public function testStates(): void
    {
        $dungeon  = $this->dungeonRepository->findById(1);
        $dungeon2 = $this->dungeonRepository->findById(2);

        self::assertFalse($this->player->isFighting());
        self::assertTrue($this->player->isInProtectiveZone());
        self::assertFalse($this->player->isInDungeon($dungeon));
        self::assertFalse($this->player->isInDungeon($dungeon2));

        $result = $this->player->enterDungeon($dungeon);
        self::assertNoErrorOccurred($result);

        self::assertTrue($this->player->isFighting());
        self::assertFalse($this->player->isInProtectiveZone());
        self::assertTrue($this->player->isInDungeon($dungeon));
        self::assertFalse($this->player->isInDungeon($dungeon2));

        $this->player->leaveDungeon();

        self::assertFalse($this->player->isFighting());
        self::assertTrue($this->player->isInProtectiveZone());
    }

    public function testEnterDungeonWhileAlreadyInOne(): void
    {
        $dungeon  = $this->dungeonRepository->findById(1);
        $dungeon2 = $this->dungeonRepository->findById(2);

        $firstEnter = $this->player->enterDungeon($dungeon);
        self::assertNoErrorOccurred($firstEnter);

        $this->setCurrentTime($this->currentTime->addMinutes(2));

        // TODO check that attempt to enter the same dungeon twice doesn't modify anything
        $secondEnter = $this->player->enterDungeon($dungeon2);
        self::assertErrorOccurred($secondEnter, 'You are already hunting in a dungeon');
    }

    #[DataProvider('dungeonDifficultyProvider')]
    public function testMeasureDifficulty(int $dungeonId, string $expectedDifficulty): void
    {
        $dungeon = $this->dungeonRepository->findById($dungeonId);

        $difficulty = $this->player->measureDifficulty($dungeon);

        self::assertSame($expectedDifficulty, $difficulty);
    }

    public static function dungeonDifficultyProvider(): iterable
    {
        yield 'easy' => [
            1,
            'easy(>50 mobs/h)',
        ];

        yield 'moderate' => [
            2,
            'moderate(<50 mobs/h)',
        ];

        yield 'impossible 1' => [
            4,
            'impossible(0 mobs/h)',
        ];

        yield 'impossible 2' => [
            3,
            'impossible(0 mobs/h)',
        ];
    }

    public function testObtainItem(): void
    {
        $gold   = $this->getService(ItemPrototypeRepository::class)->getById(1);
        $cheese = $this->getService(ItemPrototypeRepository::class)->getById(2);

        $this->player->obtainItem(new Item($gold->id, 123));
        $this->player->obtainItem(new Item($cheese->id, 3));
        $this->player->obtainItem(new Item($gold->id, 15));

        self::assertEquals(123 + 15, $this->player->getGold());

        /** @var Item[] $itemsInInventory */
        $itemsInInventory = iterator_to_array($this->player->getInventory());

        self::assertCount(2, $itemsInInventory);
        self::assertEquals($gold->id, $itemsInInventory[0]->id);
        self::assertEquals(123 + 15, $itemsInInventory[0]->quantity);
        self::assertEquals($gold->worth, $itemsInInventory[0]->worth);
        self::assertEquals($gold->isSellable(), $itemsInInventory[0]->isSellable);

        self::assertEquals($cheese->id, $itemsInInventory[1]->id);
        self::assertEquals(3, $itemsInInventory[1]->quantity);
        self::assertEquals($cheese->worth, $itemsInInventory[1]->worth);
        self::assertEquals($cheese->isSellable(), $itemsInInventory[1]->isSellable);
    }

    public function testPickUp(): void
    {
        $gold   = $this->getService(ItemPrototypeRepository::class)->getById(1);
        $cheese = $this->getService(ItemPrototypeRepository::class)->getById(2);

        $this->player->pickUp(new Item($gold->id, 123));
        $this->player->pickUp(new Item($cheese->id, 3));
        $this->player->pickUp(new Item($gold->id, 15));

        self::assertEquals(123 + 15, $this->player->getGold());

        /** @var Item[] $itemsInInventory */
        $itemsInInventory = iterator_to_array($this->player->getInventory());

        self::assertCount(2, $itemsInInventory);
        self::assertEquals($gold->id, $itemsInInventory[0]->id);
        self::assertEquals(123 + 15, $itemsInInventory[0]->quantity);
        self::assertEquals($gold->worth, $itemsInInventory[0]->worth);
        self::assertEquals($gold->isSellable(), $itemsInInventory[0]->isSellable);

        self::assertEquals($cheese->id, $itemsInInventory[1]->id);
        self::assertEquals(3, $itemsInInventory[1]->quantity);
        self::assertEquals($cheese->worth, $itemsInInventory[1]->worth);
        self::assertEquals($cheese->isSellable(), $itemsInInventory[1]->isSellable);
    }

    public function testStartActivity(): void
    {
        self::assertNull($this->player->getCurrentActivity());

        $activity = Activity::lumberjack(1);
        $result   = $this->player->startActivity($activity);
        $this->assertNoErrorOccurred($result);

        $currentActivity = $this->player->getCurrentActivity();
        self::assertNotNull($currentActivity);

        self::assertTrue($currentActivity->isSame($activity));
    }

    public function testStopActivity(): void
    {
        $activity = Activity::lumberjack(1);
        $result   = $this->player->startActivity($activity);
        $this->assertNoErrorOccurred($result);

        $currentActivity = $this->player->getCurrentActivity();
        self::assertNotNull($currentActivity);

        $this->player->stopActivity();

        self::assertNull($this->player->getCurrentActivity());
    }
}
