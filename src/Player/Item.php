<?php
declare(strict_types=1);

namespace Game\Player;

use Game\ItemId;

readonly class Item
{
    public string $name;

    public function __construct(public ItemId $id, public int $quantity, public int $worth)
    {
        $this->name = $id->name;
    }

    public function isSellable(): bool
    {
        return $this->id !== ItemId::GOLD;
    }
}