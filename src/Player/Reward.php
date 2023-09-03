<?php

declare(strict_types=1);

namespace Game\Player;

use Game\Item\Item;

readonly class Reward
{
    /**
     * @var Item[]
     */
    public array $items;

    public static function none(): self
    {
        return new self(0, []);
    }

    /**
     * @param int    $exp
     * @param Item[] $items
     */
    public function __construct(public int $exp, array $items)
    {
        $this->items = $this->groupItems($items);
    }

    public function isEmpty(): bool
    {
        return $this->exp === 0 && $this->items === [];
    }

    /**
     * @param Item[] $items
     *
     * @return Item[]
     */
    private function groupItems(array $items): array
    {
        /** @var Item[] $groupedItems */
        $groupedItems = [];
        foreach ($items as $item) {
            $itemId = $item->id;
            if (isset($groupedItems[$itemId])) {
                $existingItem          = $groupedItems[$itemId];
                $groupedItems[$itemId] = new Item($existingItem->id, $existingItem->quantity + $item->quantity);
            } else {
                $groupedItems[$itemId] = $item;
            }
        }

        return array_values($groupedItems);
    }

    public function multiply(float $modifier): self
    {
        if ($modifier < 0) {
            throw new \RuntimeException('Negative modifier is not allowed');
        }

        if ($modifier == 0) {
            return self::none();
        }

        $newExp   = (int) round($this->exp * $modifier);
        $newItems = [];
        foreach ($this->items as $item) {
            $newAmount = (int) round($item->quantity * $modifier);
            if ($newAmount === 0) {
                continue;
            }
            $newItems[] = new Item($item->id, $newAmount);
        }

        return new self($newExp, $newItems);
    }
}
