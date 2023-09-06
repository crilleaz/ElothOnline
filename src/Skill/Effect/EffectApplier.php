<?php

declare(strict_types=1);

namespace Game\Skill\Effect;

use Game\Engine\Error;
use Game\Player\Player;
use Game\Skill\Effect;

class EffectApplier
{
    public static function apply(Effect $effect, Player $player): ?Error
    {
        if ($effect->type === EffectType::RESTORE_STAMINA) {
            $player->restoreStamina($effect->power);

            return null;
        }

        // @phpstan-ignore-next-line
        throw new \RuntimeException('Effect type is unknown');
    }
}
