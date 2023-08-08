<?php
declare(strict_types=1);

namespace Game;

class LvlCalculator
{
    public static function convertExpToLvl(int $exp): int
    {
        $lvl = 1;
        do {
            $lvlExpRequirement = self::minExpRequired($lvl);
            $lvl++;
        } while ($exp > $lvlExpRequirement);

        return $lvl;
    }

    public static function minExpRequired(int $forLevel): int
    {
        if ($forLevel < 5){
            return ($forLevel - 1) * 100;
        }

        return (int) (floor(((4.32126 * pow($forLevel, 3.35211)) - 185.195)/100) * 100);
    }
}
