<?php
declare(strict_types=1);

namespace Game\Item;

use Game\Engine\DBConnection;

final class ItemPrototypeRepository
{
    private static array $cache = [];

    public function __construct(private readonly DBConnection $db) {}

    public function getById(int $id): ItemPrototype
    {
        if (!isset(self::$cache[$id])) {
            $prototype = $this->db->fetchRow('SELECT * FROM items WHERE item_id=' . $id);
            if ($prototype === []) {
                throw new \RuntimeException('Unknown item');
            }

            self::$cache[$id] = new ItemPrototype($id, $prototype['name'], (int) $prototype['worth']);
        }

        return self::$cache[$id];
    }
}
