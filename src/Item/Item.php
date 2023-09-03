<?php

declare(strict_types=1);

namespace Game\Item;

use Game\Skill\Effect;
use Game\Skill\EffectRepository;

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
        $prototype        = \DI::getService(ItemPrototypeRepository::class)->getById($itemId);
        $this->prototype  = $prototype;
        $this->id         = $prototype->id;
        $this->name       = $prototype->name;
        $this->quantity   = $quantity;
        $this->worth      = $prototype->worth;
        $this->isSellable = $prototype->isSellable();
// todo replace with Type
    }

    /**
     * @return iterable<Effect>
     */
    public function listEffects(): iterable
    {
        return \DI::getService(EffectRepository::class)->findByItem($this->id);
    }

    public function isConsumable(): bool
    {
        // TODO implement item types
        return $this->id === 2;
    }
}
