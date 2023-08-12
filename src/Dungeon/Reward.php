<?php
declare(strict_types=1);

namespace Game\Dungeon;

readonly class Reward
{
    /**
     * @var Drop[]
     */
    public array $items;

    public static function none(): self
    {
        return new self(0, []);
    }

    public function __construct(public int $exp, array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof Drop) {
                throw new \UnexpectedValueException(sprintf('Expected Drop. Got "%s"', print_r($item, true)));
            }
        }

        $this->items = $items;
    }

    public function isEmpty(): bool
    {
        return $this->exp === 0 && $this->items === [];
    }
}
