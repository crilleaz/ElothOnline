<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Client;
use Game\Trade\ShopRepository;
use Game\UI\Scene\Input\InputInterface;
use Twig\Environment;

class Shop extends AbstractScene
{
    public function __construct(Client $game, Environment $renderer, DungeonRepository $dungeonRepository, private readonly ShopRepository $shopRepository)
    {
        parent::__construct($game, $renderer, $dungeonRepository);
    }

    public function run(InputInterface $input): string
    {
        $shopName = $input->getString('shop');
        if ($shopName === '') {
            return $this->switchToScene(Shops::class);
        }

        $shop = $this->shopRepository->findShopByName($shopName);
        if ($shop === null) {
            return $this->switchToScene(Shops::class);
        }

        $player = $this->client->getCurrentPlayer();

        return $this->renderTemplate('shop', [
            'playerGold' => $player->getGold(),
            'shop' => $shop,
        ]);
    }
}
