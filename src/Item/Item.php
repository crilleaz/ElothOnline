<?php
declare(strict_types=1);

namespace Game\Item;

readonly class Item
{
    public int $id;

    public string $name;
    public int $quantity;
    public int $worth;
    public bool $isSellable;

    public function __construct(ItemPrototype $prototype, int $quantity)
    {
        $this->id =  $prototype->id;
        $this->name = $prototype->name;
        $this->quantity = $quantity;
        $this->worth = $prototype->worth;
        $this->isSellable = $prototype->isSellable();
    }
}
