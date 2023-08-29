<?php
declare(strict_types=1);

use Game\UI\Scene;
use Game\UI\Scene\Input\HttpInput;

require_once __DIR__ . '/../bootstrap.php';

// TODO move to the Front controller or something like that
enum Navigation: string
{
    case AUTH = 'auth';
    case DUNGEONS = 'dungeons';
    case HIGH_SCORE = 'highscores';
    case MAIN = 'main';
    case INVENTORY = 'inventory';
    case LIBRARY = 'library';
    case SHOPS = 'shops';

    case SHOP = 'shop';

    public function load(): void
    {
        $userInput = new HttpInput();
        echo $this->getScene()->run($userInput);
    }

    private function getScene(): Scene\SceneInterface
    {
        switch ($this) {
            case self::MAIN:
                return DI::getService(Scene\MainMenu::class);
            case self::DUNGEONS:
                return DI::getService(Scene\Dungeons::class);
            case self::LIBRARY:
                return DI::getService(Scene\Library::class);
            case self::HIGH_SCORE:
                return DI::getService(Scene\Highscore::class);
            case self::INVENTORY:
                return DI::getService(Scene\Inventory::class);
            case self::SHOPS:
                return DI::getService(Scene\Shops::class);
            case self::SHOP:
                return DI::getService(Scene\Shop::class);
            default:
                return DI::getService(Scene\Auth::class);
        }
    }
}

session_start();

if (isset($_GET['scene']) && is_string($_GET['scene'])) {
    $navigation = Navigation::tryFrom($_GET['scene']);
    if ($navigation === null) {
        exit('Unknown scene');
    }
} else if (DI::getService(\Game\Client::class)->getCurrentPlayer() !== null) {
    $navigation = Navigation::MAIN;
} else {
    $navigation = Navigation::AUTH;
}

$navigation->load();
