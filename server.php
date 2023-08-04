<?php
include("engine.php");

$resultLogs = \Game\Game::instance()->engine->performTasks();

foreach ($resultLogs as $log) {
    echo $log . PHP_EOL;
}

echo PHP_EOL;
echo 'Cron executed: ' . date("Y-m-d H:i:s") . PHP_EOL;
