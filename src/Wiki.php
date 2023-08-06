<?php
declare(strict_types=1);

namespace Game;

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

            yield new Dungeon((int)$dungeon['id'], $dungeon['name'], $dungeon['description'], $monster, (int)$dungeon['difficult']);
        }
    }

    public function getMonsters(): iterable
    {
        $monsters = $this->connection->fetchRows('SELECT name, health, experience, attack, defense FROM monster');

        foreach ($monsters as $monsterData) {
            yield new Monster(
                $monsterData['name'],
                $monsterData['health'],
                $monsterData['experience'],
                $monsterData['attack'],
                $monsterData['defense']
            );
        }
    }

    private function getMonster(int $monsterId): Monster
    {
        $monsterData = $this->connection->fetchRow('SELECT * FROM monster WHERE id = ' . $monsterId);

        return new Monster(
            $monsterData['name'],
            $monsterData['health'],
            $monsterData['experience'],
            $monsterData['attack'],
            $monsterData['defense']
        );
    }
}