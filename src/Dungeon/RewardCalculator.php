<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Player\Player;
use Game\Utils\TimeInterval;

readonly class RewardCalculator
{
    private readonly TTKCalculator $ttkCalculator;

    public function __construct(private DropChanceRepository $dropRepository)
    {
        $this->ttkCalculator = new TTKCalculator();
    }

    public function calculate(Dungeon $dungeon, Player $hunter, TimeInterval $timesSpentInDungeon): Reward
    {
        $approximateSpentMinutes = (int)round($timesSpentInDungeon->toMinutes());
        if ($approximateSpentMinutes === 0) {
            return Reward::none();
        }

        $ttkMonster = $this->ttkCalculator->calculate($hunter, $dungeon->inhabitant);
        $ttkPlayer = $this->ttkCalculator->calculateForMonster($dungeon->inhabitant, $hunter);

        $unitsKilled = (int)floor($approximateSpentMinutes / $ttkMonster->toMinutes());
        if ($unitsKilled === 0) {
            return Reward::none();
        }

        // If player needs more time to kill monster than monsters needs to kill player, then issue no rewards
        if ($ttkMonster->isGreaterThan($ttkPlayer)) {
            return Reward::none();
        }

        $expEarned = $unitsKilled * $dungeon->inhabitant->exp;

        $drops = [];
        foreach ($this->dropRepository->getMonsterDrop($dungeon->inhabitant) as $dropChance) {
            $quantity = $dropChance->roll($unitsKilled);
            if ($quantity > 0) {
                $drops[] = new Drop($dropChance->itemPrototype, $quantity);
            }
        }

        return new Reward($expEarned, $drops);
    }
}
