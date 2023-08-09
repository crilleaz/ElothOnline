<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Engine\TimeInterval;
use Game\Player\Player;

readonly class RewardCalculator
{
    public function __construct(private DropRepository $dropRepository)
    {

    }

    public function calculate(Dungeon $dungeon, Player $hunter, TimeInterval $timesSpentInDungeon): Reward
    {
        $approximateSpentMinutes = (int) round($timesSpentInDungeon->toMinutes());
        $stamina = $hunter->getStamina();

        // Prevents over-rewarding the player (1 stamina for 1 minute is how it should be).
        // Thus, it decreases the passed time for a player as if he has left the dungeon after depleting the stamina
        if ($stamina < $approximateSpentMinutes) {
            $approximateSpentMinutes = $stamina;
        }

        $expEarned = $dungeon->inhabitant->exp * $approximateSpentMinutes;

        $drop = [$this->dropRepository->getMonsterDrop($dungeon->inhabitant)];

        return new Reward($expEarned, $drop);
    }
}