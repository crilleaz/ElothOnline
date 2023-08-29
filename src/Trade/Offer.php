<?php
declare(strict_types=1);

namespace Game\Trade;

use Game\Item\Item;

readonly class Offer
{
    public function __construct(public Item $item, public Item $inExchange)
    {

    }
}
