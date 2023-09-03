<?php

declare(strict_types=1);

namespace Game\Player;

readonly class Perks
{
    public function __construct(
        public bool $canCraft,
        public bool $canMine,
        public bool $canWoodcut,
        public bool $canHarvest,
        public bool $canGather,
        public bool $canBrew
    ) {
    }
}
