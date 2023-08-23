<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Game;
use Game\Wiki;
use Twig\Environment;

class Shops extends AbstractScene
{
    public function __construct(Game $game, Environment $renderer, private readonly Wiki $wiki)
    {
        parent::__construct($game, $renderer);
    }

    public function run(): string
    {
        $player = $this->game->getCurrentPlayer();

        return $this->renderTemplate('shops', [
            'player' => $player,
            'shops' => $this->wiki->getShops(),
        ]);
    }
}
