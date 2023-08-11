<?php
declare(strict_types=1);

namespace Game\Dungeon;

readonly class Reward
{
    /**
     * @var Drop[]
     */
    public array $items;

    public function __construct(public int $exp, array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof Drop) {
                throw new \UnexpectedValueException(sprintf('Expected Drop. Got "%s"', print_r($item, true)));
            }
        }

        $this->items = $items;
    }
}