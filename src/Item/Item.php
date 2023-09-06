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
        $this->prototype  = \DI::getService(ItemPrototypeRepository::class)->getById($itemId);
        $this->id         = $this->prototype->id;
        $this->name       = $this->prototype->name;
        $this->quantity   = $quantity;
        $this->worth      = $this->prototype->worth;
        $this->isSellable = $this->prototype->isSellable();
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
        return $this->prototype->type === ItemType::CONSUMABLE;
    }
}
