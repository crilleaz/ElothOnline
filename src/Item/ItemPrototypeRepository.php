<?php
declare(strict_types=1);

namespace Game\Item;

use Game\Engine\DBConnection;

final class ItemPrototypeRepository
{
    private static array $cache = [];

    public function __construct(private readonly DBConnection $db) {}

    public function getById(ItemId $id): ItemPrototype
    {
        if (!isset(self::$cache[$id->value])) {
            $prototype = $this->db->fetchRow('SELECT * FROM items WHERE id=' . $id->value);
            if ($prototype === []) {
                throw new \RuntimeException('Unknown item');
            }

            self::$cache[$id->value] = new ItemPrototype($id, $prototype['name'], (int) $prototype['worth']);
        }

        return self::$cache[$id->value];
    }
}