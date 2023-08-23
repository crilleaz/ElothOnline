<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Game;
use Game\Wiki;
use Twig\Environment;

class Library extends AbstractScene
{
    public function __construct(Game $game, Environment $renderer, private readonly Wiki $wiki)
    {
        parent::__construct($game, $renderer);
    }

    public function run(): string
    {
        return $this->renderTemplate('library', [
            'player' => $this->game->getCurrentPlayer(),
            'monsters' => $this->wiki->getMonsters(),
        ]);
    }
}
