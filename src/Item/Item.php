<?php
declare(strict_types=1);

namespace Game\Item;

use Game\Engine\DBConnection;
use Game\Skill\Effect;
use Game\Skill\Effect\EffectType;

readonly class Item
{
    public int $id;

    public string $name;
    public int $quantity;
    public int $worth;
    public bool $isSellable;

    public ItemPrototype $prototype;

    public function __construct(int $itemId, int $quantity)
    {
        $prototype = \DI::getService(ItemPrototypeRepository::class)->getById($itemId);
        $this->prototype = $prototype;
        $this->id =  $prototype->id;
        $this->name = $prototype->name;
        $this->quantity = $quantity;
        $this->worth = $prototype->worth;
        $this->isSellable = $prototype->isSellable(); // todo replace with Type
    }

    /**
     * @return Effect[]
     */
    public function listEffects(): array
    {
        $entries = \DI::getService(DBConnection::class)->fetchRows('SELECT * FROM item_effect WHERE item_id=' . $this->id);

        if ($entries === []) {
            return [];
        }

        $effects = [];
        foreach ($entries as $entry) {
            // todo item effects should be immutable data source
            // todo move every immutable data source into files/memory. Don't use db for that
            $effects[] = new Effect($entry['name'], EffectType::from($entry['type']), $entry['power']);
        }

        return $effects;
    }

    public function isConsumable(): bool
    {
        // TODO implement item types
        return $this->id === 2;
    }
}
