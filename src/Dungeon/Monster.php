<?php

declare(strict_types=1);

namespace Game\Dungeon;

readonly class Monster
{
    public function __construct(
        public int $id,
        public string $name,
        public int $health,
        public int $exp,
        public int $attack,
        public int $defence
    ) {
    }
}
