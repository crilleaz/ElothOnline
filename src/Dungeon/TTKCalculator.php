<?php

declare(strict_types=1);

namespace Game\Dungeon;

use Game\Player\Player;
use Game\Utils\TimeInterval;

/**
 * Time to kill calculator
 */
class TTKCalculator
{
    private const ATTACK_PER = 'minute';

    public function calculate(Player $hunter, Monster $prey): TimeInterval
    {
        return $this->calculateTtk($hunter->getStrength(), $prey->defence, $prey->health);
    }

    public function calculateForMonster(Monster $hunter, Player $prey): TimeInterval
    {
        return $this->calculateTtk($hunter->attack, $prey->getDefence(), $prey->getCurrentHealth());
    }

    private function calculateTtk(int $attack, int $defence, int $health): TimeInterval
    {
        $mitigatedDamage = $attack - $defence;
        // If defences are higher than attack, then we assume it a scratch and deal 1 damage
        if ($mitigatedDamage < 1) {
            $mitigatedDamage = 1;
        }

        $hitsRequired = (int) ceil($health / $mitigatedDamage);

        switch (self::ATTACK_PER) {
            case 'second':
                return new TimeInterval($hitsRequired);
            case 'minute':
                return TimeInterval::fromMinutes($hitsRequired);
            default:
                throw new \RuntimeException('Attack speed is not supported');
        }
    }
}
