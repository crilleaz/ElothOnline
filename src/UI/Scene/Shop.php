<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Game;
use Game\Trade\ShopRepository;
use Twig\Environment;

class Shop extends AbstractScene
{
    public function __construct(Game $game, Environment $renderer, DungeonRepository $dungeonRepository, private readonly ShopRepository $shopRepository)
    {
        parent::__construct($game, $renderer, $dungeonRepository);
    }

    public function run(): string
    {
        $shopName = $_GET['shop'] ?? '';

        if (!is_string($shopName) || $shopName === '') {
            return $this->switchToScene(Shops::class);
        }

        $shop = $this->shopRepository->findShopByName($shopName);
        if ($shop === null) {
            return $this->switchToScene(Shops::class);
        }

        $player = $this->game->getCurrentPlayer();

        return $this->renderTemplate('shop', [
            'playerGold' => $player->getGold(),
            'shop' => $shop,
        ]);
    }
}
