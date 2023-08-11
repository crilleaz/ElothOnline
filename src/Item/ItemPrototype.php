<?php
declare(strict_types=1);

namespace Game\Item;

readonly class ItemPrototype
{
    public function __construct(public ItemId $id, public string $name, public int $worth)
    {
    }

    public function isSellable(): bool
    {
        return $this->id !== ItemId::GOLD;
    }
}