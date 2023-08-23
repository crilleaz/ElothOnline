<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Game;
use Game\Wiki;
use Twig\Environment;

class Shop extends AbstractScene
{
    public function __construct(Game $game, Environment $renderer, private readonly Wiki $wiki)
    {
        parent::__construct($game, $renderer);
    }

    public function run(): string
    {
        $shopName = $_GET['shop'] ?? '';

        if (!is_string($shopName) || $shopName === '') {
            return $this->switchToScene(Shops::class);
        }

        $shop = $this->wiki->findShop($shopName);
        if ($shop === null) {
            return $this->switchToScene(Shops::class);
        }


        $player = $this->game->getCurrentPlayer();

        return $this->renderTemplate('shop', [
            'player' => $player,
            'playerGold' => $player->getGold(),
            'shop' => $shop,
        ]);
    }
}
