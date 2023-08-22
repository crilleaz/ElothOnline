<?php
declare(strict_types=1);

namespace Game;

use Game\Dungeon\Dungeon;
use Game\Dungeon\Monster;
use Game\Engine\DBConnection;
use Game\Item\ItemPrototypeRepository;
use Game\Trade\Shop;

readonly class Wiki
{
    public function __construct(private DBConnection $connection) {}

    /**
     * @return iterable<Dungeon>
     */
    public function getDungeons(): iterable
    {
        foreach ($this->connection->fetchRows('SELECT * FROM dungeons') as $dungeon) {
            $monster = $this->getMonster((int)$dungeon['monster_id']);

            yield new Dungeon((int)$dungeon['id'], $dungeon['name'], $dungeon['description'], $monster);
        }
    }

    /**
     * @return iterable<Shop>
     */
    public function getShops(): iterable
    {
        $shops = $this->connection->fetchRows('SELECT * FROM shop');

        foreach ($shops as $shop) {
            yield new Shop($shop['id'], $shop['name'], $shop['description'], $this->connection, \DI::getService(ItemPrototypeRepository::class));
        }
    }

    // TODO likely not the best place to keep this method
    public function getShop(string $name): Shop
    {
        $shop = $this->connection->fetchRow('SELECT * FROM shop WHERE name=?', [$name]);

        return new Shop($shop['id'], $shop['name'], $shop['description'], $this->connection, \DI::getService(ItemPrototypeRepository::class));
    }

    public function getMonsters(): iterable
    {
        $monsters = $this->connection->fetchRows('SELECT monster_id as id, name, health, experience, attack, defence FROM monster');

        foreach ($monsters as $monsterData) {
            yield new Monster(
                $monsterData['id'],
                $monsterData['name'],
                $monsterData['health'],
                $monsterData['experience'],
                $monsterData['attack'],
                $monsterData['defence']
            );
        }
    }

    private function getMonster(int $monsterId): Monster
    {
        $monsterData = $this->connection->fetchRow('SELECT * FROM monster WHERE monster_id = ' . $monsterId);

        return new Monster(
            $monsterId,
            $monsterData['name'],
            $monsterData['health'],
            $monsterData['experience'],
            $monsterData['attack'],
            $monsterData['defence']
        );
    }
}
