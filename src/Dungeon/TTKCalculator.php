<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Engine\TimeInterval;
use Game\Player\Player;

/**
 * Time to kill calculator
 */
class TTKCalculator
{
    private const ATTACK_PER = 'second';

    public function calculate(Player $hunter, Monster $prey): TimeInterval
    {
        $mitigatedDamage = $hunter->getStrength() - $prey->defence;
        // If defences are higher than attack, then we assume it a scratch and deal 1 damage
        if ($mitigatedDamage < 0) {
            $mitigatedDamage = 1;
        }

        $hitsRequired = (int)ceil($prey->health / $mitigatedDamage);

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
