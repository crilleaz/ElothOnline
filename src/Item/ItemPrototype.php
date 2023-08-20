<?php
declare(strict_types=1);

namespace Game\Item;

readonly class ItemPrototype
{
    public function __construct(public int $id, public string $name, public int $worth)
    {
    }

    public function isSellable(): bool
    {
        // TODO add property is_sellable into db
        return $this->id !== 1;
    }
}
