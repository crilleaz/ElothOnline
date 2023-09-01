<?php
declare(strict_types=1);

namespace Game\Dungeon;

class Reward
{
    /**
     * @var Drop[]
     */
    private array $drop = [];

    public static function none(): self
    {
        return new self(0, []);
    }

    /**
     * @param int $exp
     * @param Drop[] $drop
     */
    public function __construct(public readonly int $exp, array $drop)
    {
        foreach ($drop as $entry) {
            $this->addDrop($entry);
        }
    }

    public function isEmpty(): bool
    {
        return $this->exp === 0 && $this->drop === [];
    }

    /**
     * @return Drop[]
     */
    public function listDrop(): array
    {
        return array_values($this->drop);
    }

    private function addDrop(Drop $drop): void
    {
        $itemId = $drop->item->id;
        if (isset($this->drop[$itemId])) {
            $existingDrop = $this->drop[$itemId];
            $this->drop[$itemId] = new Drop($existingDrop->item, $existingDrop->quantity + $drop->quantity);
        } else {
            $this->drop[$itemId] = $drop;
        }
    }

    public function multiply(float $modifier): static
    {
        $newExp = (int) round($this->exp * $modifier);
        $newDrops = [];
        foreach ($this->drop as $drop) {
            $newAmount = (int) round($drop->quantity * $modifier);
            if ($newAmount === 0) {
                continue;
            }
            $newDrops[] = new Drop($drop->item, $newAmount);
        }

        return new self($newExp, $newDrops);
    }
}
