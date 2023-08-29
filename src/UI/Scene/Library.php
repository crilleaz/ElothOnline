<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Dungeon\MonsterRepository;
use Game\Game;
use Twig\Environment;

class Library extends AbstractScene
{
    public function __construct(
        Game $game,
        Environment $renderer,
        DungeonRepository $dungeonRepository,
        private readonly MonsterRepository $monsterRepository)
    {
        parent::__construct($game, $renderer, $dungeonRepository);
    }

    public function run(): string
    {
        return $this->renderTemplate('library', [
            'monsters' => $this->monsterRepository->listMonsters(),
        ]);
    }
}
