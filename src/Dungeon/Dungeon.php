<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Engine\DBConnection;

readonly class Dungeon
{
    private const SQL_DUNGEON_BY_ID = 'SELECT d.id, d.name, d.description, m.name as monsterName, m.health, m.attack, m.defence, m.experience
                                 FROM dungeons d
                                    INNER JOIN monster m ON d.monster_id = m.id
                                 WHERE d.id=?
    ';

    public static function loadById(int $id, DBConnection $db): ?self
    {
        $dungeon = $db->fetchRow(self::SQL_DUNGEON_BY_ID, [$id]);

        if ($dungeon === []) {
            return null;
        }

        return new self(
            $dungeon['id'],
            $dungeon['name'],
            $dungeon['description'],
            new Monster($dungeon['monsterName'], $dungeon['health'], $dungeon['experience'], $dungeon['attack'], $dungeon['defence'])
        );
    }

    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public Monster $inhabitant
    ) {}
}
