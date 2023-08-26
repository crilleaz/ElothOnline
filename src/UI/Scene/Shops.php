<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Game;
use Game\Trade\ShopRepository;
use Twig\Environment;

class Shops extends AbstractScene
{
    public function __construct(Game $game, Environment $renderer, DungeonRepository $dungeonRepository, private readonly ShopRepository $shopRepository)
    {
        parent::__construct($game, $renderer, $dungeonRepository);
    }

    public function run(): string
    {
        $player = $this->game->getCurrentPlayer();

        return $this->renderTemplate('shops', [
            'player' => $player,
            'shops' => $this->shopRepository->listShops(),
        ]);
    }
}
