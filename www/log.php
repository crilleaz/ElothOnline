<?php

require_once __DIR__ . '/../bootstrap.php';

session_start();

$player = getService(\Game\Game::class)->getCurrentPlayer();
if ($player === null) {
    header('Location: /');

    exit();
}

foreach ($player->getLogs(5) as $log) {
    echo $log . '<br>';
}
