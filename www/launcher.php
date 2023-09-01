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
    case CHARACTER_CREATION = 'charCreation';

    case ACTIVITY_LUMBERJACK = 'lumberjack';

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
            case self::CHARACTER_CREATION:
                return DI::getService(Scene\CharacterCreation::class);
            case self::ACTIVITY_LUMBERJACK:
                return DI::getService(Scene\Activity\Lumberjack::class);
            default:
                return DI::getService(Scene\Auth::class);
        }
    }
}

session_start();

$gameClient = DI::getService(\Game\Client::class);

if (!$gameClient->isRunning()) {
    $navigation = Navigation::AUTH;
} elseif ($gameClient->getCurrentPlayer() === null) {
    $navigation = Navigation::CHARACTER_CREATION;
} else if (isset($_GET['scene']) && is_string($_GET['scene'])) {
    $navigation = Navigation::tryFrom($_GET['scene']);
    if ($navigation === null) {
        exit('Unknown scene');
    }
} else {
    $navigation = Navigation::MAIN;
}

$navigation->load();
