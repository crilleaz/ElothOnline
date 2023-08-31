<?php
declare(strict_types=1);

namespace Game\Player;

readonly class Stats
{
    public function __construct(
        public int $maxHealth,
        public int $strength,
        public int $defence,
    ) {}
}
