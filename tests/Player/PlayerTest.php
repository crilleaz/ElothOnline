<?php
declare(strict_types=1);

namespace Game\Player;

use Game\Dungeon\Drop;
use Game\Dungeon\Dungeon;
use Game\IntegrationTestCase;
use Game\Item\Item;
use Game\Item\ItemPrototypeRepository;
use PHPUnit\Framework\Attributes\DataProvider;

class PlayerTest extends IntegrationTestCase
{
    private Player $player;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db->execute("INSERT INTO players(name, experience, health, health_max, defence, strength)
                            VALUES ('Mick', '0', 120, 120, 5, 100)");
        $this->player = Player::loadPlayer('Mick', $this->db);
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
        $dungeon = Dungeon::loadById(1, $this->db);
        $dungeon2 = Dungeon::loadById(2, $this->db);

        self::assertFalse($this->player->isFighting());
        self::assertTrue($this->player->isInProtectiveZone());
        self::assertFalse($this->player->isInDungeon($dungeon));
        self::assertFalse($this->player->isInDungeon($dungeon2));

        $result = $this->player->enterDungeon($dungeon->id);
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
        $dungeon = Dungeon::loadById(1, $this->db);
        $dungeon2 = Dungeon::loadById(2, $this->db);

        $firstEnter = $this->player->enterDungeon($dungeon->id);
        self::assertNoErrorOccurred($firstEnter);

        $this->setCurrentTime($this->currentTime->addMinutes(2));

        $enterSameDungeon = $this->player->enterDungeon($dungeon->id);
        self::assertNoErrorOccurred($enterSameDungeon);
        // TODO check that attempt to enter the same dungeon twice doesn't modify anything

        $secondEnter = $this->player->enterDungeon($dungeon2->id);
        self::assertErrorOccurred($secondEnter, 'You are already hunting at ' . $dungeon->name);
    }

    #[DataProvider('dungeonDifficultyProvider')]
    public function testMeasureDifficulty(int $dungeonId, string $expectedDifficulty): void
    {
        $dungeon = Dungeon::loadById($dungeonId, $this->db);

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

        yield 'hard' => [
            4,
            'hard(<20 mobs/h)',
        ];

        yield 'impossible' => [
            3,
            'impossible(0 mobs/h)',
        ];
    }

    public function testObtainItem(): void
    {
        $gold = $this->getService(ItemPrototypeRepository::class)->getById(1);
        $cheese = $this->getService(ItemPrototypeRepository::class)->getById(2);

        $this->player->obtain($gold, 123);
        $this->player->obtain($cheese, 3);
        $this->player->obtain($gold, 15);

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
        $gold = $this->getService(ItemPrototypeRepository::class)->getById(1);
        $cheese = $this->getService(ItemPrototypeRepository::class)->getById(2);

        $this->player->pickUp(new Drop($gold, 123));
        $this->player->pickUp(new Drop($cheese, 3));
        $this->player->pickUp(new Drop($gold, 15));

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
}
