<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Engine\DBConnection;
use Game\Item\ItemPrototype;
use Game\Utils\Chance;

readonly class DropChanceRepository
{
    public function __construct(private DBConnection $db)
    {
    }

    /**
     * @param Monster $monster
     *
     * @return iterable<DropChance>
     */
    public function getMonsterDrop(Monster $monster): iterable
    {
        $dropList = $this->db->fetchRows('SELECT * FROM droplist JOIN items i ON droplist.item_id = i.item_id WHERE monster_id=?', [$monster->id]);

        foreach ($dropList as $dropDetails) {
            yield new DropChance(
                Chance::percentage((float)$dropDetails['chance']),
                new ItemPrototype($dropDetails['item_id'], $dropDetails['name'], $dropDetails['worth']),
                $dropDetails['quantity_min'],
                $dropDetails['quantity_max']
            );
        }
    }
}
