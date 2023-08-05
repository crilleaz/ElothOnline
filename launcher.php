<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

enum Tab: string
{
    case CHAT = 'chat';
    case DUNGEON = 'dungeons';
    case HIGH_SCORE = 'highscores';
    case MAIN = 'main';
    case INVENTORY = 'inventory';
    case LIBRARY = 'library';

    public function load(): void
    {
        // TODO temporary. needs to be removed and replaced with appropriate services/models
        $db = include 'db.php';;

        $currentUserName = $_SESSION['username'] ?? '';
        if ($currentUserName === '') {
            header('Location: /login.php');
            exit();
        }

        $player = \Game\Game::instance()->findPlayer($currentUserName);

        require __DIR__ . '/tabs/' . $this->value . '.php';
    }
}

session_start();

$currentTab = Tab::MAIN;
if (isset($_GET['tab']) && is_string($_GET['tab'])) {
    $currentTab = Tab::from($_GET['tab']);
}

$currentTab->load();
