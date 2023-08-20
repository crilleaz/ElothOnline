<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Item\ItemPrototype;

readonly class Drop
{
    public function __construct(public ItemPrototype $item, public int $quantity)
    {

    }
}
