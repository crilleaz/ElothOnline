<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Client;
use Game\Trade\ShopRepository;
use Game\UI\Scene\Input\InputInterface;
use Twig\Environment;

class Shops extends AbstractScene
{
    public function __construct(Client $game, Environment $renderer, DungeonRepository $dungeonRepository, private readonly ShopRepository $shopRepository)
    {
        parent::__construct($game, $renderer, $dungeonRepository);
    }

    public function run(InputInterface $input): string
    {
        $player = $this->client->getCurrentPlayer();

        return $this->renderTemplate('shops', [
            'player' => $player,
            'shops' => $this->shopRepository->listShops(),
        ]);
    }
}
