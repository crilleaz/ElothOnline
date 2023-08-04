<?php

session_start();

require_once 'engine.php';

$username = $_SESSION['username'] ?? '';
if ($username == '') {
    header('Location: /');

    exit();
}

$player = \Game\Game::instance()->findPlayer($username);
foreach ($player->getLogs(5) as $log) {
    echo $log . '<br>';
}