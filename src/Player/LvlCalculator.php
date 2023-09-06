<?php

declare(strict_types=1);

namespace Game\Player;

class LvlCalculator
{
    public static function convertExpToLvl(int $exp): int
    {
        $lvl = 0;
        do {
            $lvl++;
            $lvlExpRequirement = self::minExpRequired($lvl);
        } while ($exp > $lvlExpRequirement);

        if ($exp === $lvlExpRequirement) {
            return $lvl;
        }

        return $lvl - 1;
    }

    public static function minExpRequired(int $forLevel): int
    {
        if ($forLevel < 5) {
            return ($forLevel - 1) * 100;
        }

        return (int) (floor(((4.32126 * pow($forLevel, 3.35211)) - 185.195) / 100) * 100);
    }
}
