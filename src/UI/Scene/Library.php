<?php

declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Dungeon\MonsterRepository;
use Game\Client;
use Game\UI\Scene\Input\InputInterface;
use Twig\Environment;

class Library extends AbstractScene
{
    public function __construct(
        Client $client,
        Environment $renderer,
        DungeonRepository $dungeonRepository,
        private readonly MonsterRepository $monsterRepository
    ) {
        parent::__construct($client, $renderer, $dungeonRepository);
    }

    public function run(InputInterface $input): string
    {
        return $this->renderTemplate('library', [
            'monsters' => $this->monsterRepository->listMonsters(),
        ]);
    }
}
