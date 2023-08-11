<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Engine\DBConnection;
use Game\Item\ItemPrototype;
use Game\Item\ItemId;

readonly class DropRepository
{
    // TODO each entry basically references database table `items`. Either it shall be moved to the code or drop to the database level.

    /**
     * @const array<string, array{id: ItemId, quantity: int}>
     */
    private const DROP = [
        'Rat' => [
            'id' => ItemId::CHEESE,
            'quantity' => 1,
        ],
        'Rotworm' => [
            'id' => ItemId::GOLD,
            'quantity' => 5,
        ],
        'Dragon Hatchling' => [
            'id' => ItemId::GOLD,
            'quantity' => 40,
        ],
        'Dragon' => [
            'id' => ItemId::SHORT_SWORD,
            'quantity' => 1,
        ],
    ];

    public function __construct(private DBConnection $db) {}

    public function getMonsterDrop(Monster $monster): Drop
    {
        if (!isset(self::DROP[$monster->name])) {
            throw new \RuntimeException(sprintf('Monster %s does not have drop info', $monster->name));
        }

        $dropDetails = self::DROP[$monster->name];
        $itemDetails = $this->db->fetchRow('SELECT * FROM items WHERE id=' . $dropDetails['id']->value);
        if ($itemDetails === []) {
            throw new \RuntimeException('Unknown item present in drop list');
        }

        return new Drop(
            new ItemPrototype($dropDetails['id'], $itemDetails['name'], $itemDetails['worth']),
            $dropDetails['quantity']
        );
    }
}