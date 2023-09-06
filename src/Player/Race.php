<?php

declare(strict_types=1);

namespace Game\Player;

readonly class Race
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public Stats $stats,
        public Perks $perks
    ) {
    }
}
