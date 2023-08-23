<?php
declare(strict_types=1);

use Game\UI\Scene;

require_once __DIR__ . '/../bootstrap.php';

enum Navigation: string
{
    case DUNGEONS = 'dungeons';
    case HIGH_SCORE = 'highscores';
    case MAIN = 'main';
    case INVENTORY = 'inventory';
    case LIBRARY = 'library';
    case SHOPS = 'shops';

    case SHOP = 'shop';

    public function load(): void
    {
        $game = DI::getService(\Game\Game::class);
        $player = $game->getCurrentPlayer();

        if ($player === null) {
            header('Location: /login.php');
            exit();
        }

        echo $this->getScene()->run();
    }

    private function getScene(): Scene\SceneInterface
    {
        switch ($this) {
            case Navigation::DUNGEONS:
                return DI::getService(Scene\Dungeons::class);
            case Navigation::LIBRARY:
                return DI::getService(Scene\Library::class);
            case Navigation::HIGH_SCORE:
                return DI::getService(Scene\Highscore::class);
            case Navigation::INVENTORY:
                return DI::getService(Scene\Inventory::class);
            case Navigation::SHOPS:
                return DI::getService(Scene\Shops::class);
            case Navigation::SHOP:
                return DI::getService(Scene\Shop::class);
            default:
                return DI::getService(Scene\MainMenu::class);
        }
    }
}

session_start();

$navigation = Navigation::MAIN;
if (isset($_GET['scene']) && is_string($_GET['scene'])) {
    $navigation = Navigation::tryFrom($_GET['scene']);
    if ($navigation === null) {
        exit('Unknown scene');
    }
}

$navigation->load();
