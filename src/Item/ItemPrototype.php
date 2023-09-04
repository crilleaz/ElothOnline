<?php

declare(strict_types=1);

namespace Game\Item;

readonly class ItemPrototype
{
    public function __construct(public int $id, public string $name, public ItemType $type, public int $worth)
    {
    }

    public function isSellable(): bool
    {
        return $this->type !== ItemType::CURRENCY;
    }
}
