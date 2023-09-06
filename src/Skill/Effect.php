<?php

declare(strict_types=1);

namespace Game\Skill;

use Game\Skill\Effect\EffectType;

readonly class Effect
{
    public function __construct(public string $name, public EffectType $type, public int $power)
    {
    }
}
