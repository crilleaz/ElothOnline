<?php

declare(strict_types=1);

namespace Game\Dungeon;

use Game\IntegrationTestCase;
use Game\Player\Player;
use Game\Utils\TimeInterval;
use PHPUnit\Framework\Attributes\DataProvider;

class TTKCalculatorTest extends IntegrationTestCase
{
    private TTKCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new TTKCalculator();
    }

    public static function ttkMonsterProvider(): iterable
    {
        yield 'monster without defence' => [
            [
        10,
        5,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 10, 0),
            TimeInterval::fromMinutes(5),
        ];

        yield 'monster has some defence' => [
            [
        10,
        5,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 10, 4),
            TimeInterval::fromMinutes(9),
        ];

        yield 'monster has too much defence' => [
            [
        10,
        5,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 10, 100),
            TimeInterval::fromMinutes(50),
        ];

        yield 'player has too much attack' => [
            [
        1000,
        5,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 10, 0),
            TimeInterval::fromMinutes(1),
        ];
    }

    public static function ttkPlayerProvider(): iterable
    {
        yield 'player without defence' => [
            [
        10,
        0,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 10, 0),
            TimeInterval::fromMinutes(10),
        ];

        yield 'player has some defence' => [
            [
        10,
        4,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 10, 0),
            TimeInterval::fromMinutes(17),
        ];

        yield 'player has too much defence' => [
            [
        2,
        1000,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 10, 0),
            TimeInterval::fromMinutes(100),
        ];

        yield 'monster has too much attack' => [
            [
        2,
        5,
        100,
            ],
            new Monster(123, 'SomeMonster', 50, 0, 1000, 0),
            TimeInterval::fromMinutes(1),
        ];
    }

    #[DataProvider('ttkMonsterProvider')]
    public function testCalculateForPlayer(array $playerStats, Monster $prey, TimeInterval $expectedTTK): void
    {
        $hunter = $this->createCharacterWithStats(...$playerStats);
        $result = $this->calculator->calculate($hunter, $prey);

        self::assertEquals($expectedTTK->seconds, $result->seconds);
    }

    #[DataProvider('ttkPlayerProvider')]
    public function testCalculateForMonster(array $playerStats, Monster $hunter, TimeInterval $expectedTTK): void
    {
        $prey   = $this->createCharacterWithStats(...$playerStats);
        $result = $this->calculator->calculateForMonster($hunter, $prey);

        self::assertEquals($expectedTTK->seconds, $result->seconds);
    }

    private function createCharacterWithStats(int $attack, int $defence, int $health): Player
    {
        $characterName = 'SomeRandomName' . uniqid();
        $character     = $this->createCharacter($characterName);

        $params = [
            $attack,
            $defence,
            $health,
            $character->getId(),
        ];

        $this->db->execute('UPDATE players SET strength = ?, defence = ?, health = ? WHERE id = ?', $params);

        return $this->getCharacterByName($characterName);
    }
}
