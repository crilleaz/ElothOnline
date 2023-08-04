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

    private function getMonster(int $monsterId): Monster
    {
        $monsterData = $this->connection->fetchRow('SELECT * FROM monster WHERE id = ' . $monsterId);

        return new Monster(
            $monsterData['name'],
            (int)$monsterData['health'],
            (int)$monsterData['experience'],
            (int)$monsterData['attack'],
            (int)$monsterData['defense']
        );
    }
}