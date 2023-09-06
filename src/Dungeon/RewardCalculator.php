<?php

declare(strict_types=1);

namespace Game\Dungeon;

use Game\Item\Item;
use Game\Player\Reward;

readonly class RewardCalculator
{
    public function __construct(private DropChanceRepository $dropRepository)
    {
    }

    public function calculateForHuntedMonster(Monster $monster, int $unitsKilled): Reward
    {
        $expEarned = $unitsKilled * $monster->exp;

        $items = [];
        foreach ($this->dropRepository->getMonsterDrop($monster) as $dropChance) {
            $quantity = $dropChance->roll($unitsKilled);
            if ($quantity > 0) {
                $items[] = new Item($dropChance->itemPrototype->id, $quantity);
            }
        }

        return new Reward($expEarned, $items);
    }
}
