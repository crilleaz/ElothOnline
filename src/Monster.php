<?php
declare(strict_types=1);

namespace Game;

readonly class Monster
{
    public function __construct(
        public string $name,
        public int    $health,
        public int    $exp,
        public int    $attack,
        public int    $defence
    )
    {
    }
}