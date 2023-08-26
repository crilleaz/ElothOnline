<?php
declare(strict_types=1);

namespace Game;

use Game\Dungeon\Dungeon;
use Game\Dungeon\DungeonRepository;
use Game\Dungeon\Monster;
use Game\Dungeon\MonsterRepository;
use Game\Engine\DBConnection;
use Game\Item\ItemPrototypeRepository;
use Game\Trade\Shop;

readonly class Wiki
{
    public function __construct(
        private readonly DungeonRepository $dungeonRepository
    ) {}

}
