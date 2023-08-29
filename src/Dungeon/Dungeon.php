<?php
declare(strict_types=1);

namespace Game\Dungeon;

readonly class Dungeon
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public Monster $inhabitant
    ) {}
}
