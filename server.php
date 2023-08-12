<?php

require_once __DIR__ . '/bootstrap.php';

$intervalInSeconds = 10;

function writeSystemLog(string $msg) {
    file_put_contents(__DIR__ . '/log.txt', $msg . PHP_EOL, FILE_APPEND);
}

$timer = \React\EventLoop\Loop::addPeriodicTimer($intervalInSeconds, function () {
    echo date("H:i:s Y-m-d") . PHP_EOL;

    $resultLogs = \Game\Game::instance()->engine->performTasks();

    foreach ($resultLogs as $log) {
        echo $log . PHP_EOL;
    }

    writeSystemLog(implode(PHP_EOL, $resultLogs));
});

echo 'Server is running';