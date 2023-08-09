<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Item\Item;

readonly class Reward
{
    /**
     * @var Item[]
     */
    public array $items;
    public function __construct(public int $exp, array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof Item) {
                throw new \UnexpectedValueException(sprintf('Expected Item class. Got "%s"', print_r($item, true)));
            }
        }

        $this->items = $items;
    }
}