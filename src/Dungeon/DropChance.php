<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Item\ItemPrototype;
use Game\Utils\Chance;

readonly class DropChance
{
    public function __construct(
        private Chance $chance,
        public ItemPrototype $itemPrototype,
        public int $quantityMin,
        public int $quantityMax
    ) {}

    /**
     * Returns quantity of items dropped.
     * 0 means nothing dropped.
     *
     * @param int $attempts amount of accumulating retries.
     *
     * @return int
     */
    public function roll(int $attempts = 1): int
    {
        if ($attempts < 1) {
            throw new \RuntimeException('Attempts can not be less than 1');
        }

        $quantity = 0;
        while ($attempts-- > 0) {
            if ($this->chance->roll()) {
                $quantity += mt_rand($this->quantityMin, $this->quantityMax);
            }
        }

        return $quantity;
    }
}
