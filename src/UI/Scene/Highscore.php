<?php

declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Client;
use Game\Dungeon\DungeonRepository;
use Game\Player\CharacterRepository;
use Game\UI\Scene\Input\InputInterface;
use Twig\Environment;

class Highscore extends AbstractScene
{
    public function __construct(Client $client, Environment $renderer, DungeonRepository $dungeonRepository, private readonly CharacterRepository $characterRepository)
    {
        parent::__construct($client, $renderer, $dungeonRepository);
    }

    public function run(InputInterface $input): string
    {
        $player     = $this->getCurrentPlayer();
        $topPlayers = $this->characterRepository->listTopCharacters(100);

        return $this->renderTemplate('highscores', [
            'player'     => $player,
            'topPlayers' => $topPlayers,
        ]);
    }
}
