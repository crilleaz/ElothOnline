<?php

require_once __DIR__ . '/../bootstrap.php';

session_start();

$username = $_SESSION['username'] ?? '';
if ($username == '') {
    header('Location: /');

    exit();
}

$player = getService(\Game\Game::class)->findPlayer($username);
foreach ($player->getLogs(5) as $log) {
    echo $log . '<br>';
}
