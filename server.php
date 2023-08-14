<?php

require_once __DIR__ . '/bootstrap.php';

$intervalInSeconds = 30.0;
$lastExecutedTime = time();

function writeSystemLog(string $msg) {
    file_put_contents(__DIR__ . '/log.txt', $msg . PHP_EOL, FILE_APPEND);
}

$timer = \React\EventLoop\Loop::addPeriodicTimer($intervalInSeconds, function () use (&$lastExecutedTime) {
    $resultLogs = getService(\Game\Engine\Engine::class)->performTasks();

    foreach ($resultLogs as $log) {
        echo $log . PHP_EOL;
    }

    writeSystemLog(implode(PHP_EOL, $resultLogs));
    $lastExecutedTime = time();
});

echo 'Server is running' . PHP_EOL;
\React\EventLoop\Loop::addPeriodicTimer(0.1, function () use ($timer, &$lastExecutedTime) {
    $nextExecutionIn = (float)($lastExecutedTime + $timer->getInterval()) - (float) time();

    printf("Next execution in %0.1f seconds\r", $nextExecutionIn) . PHP_EOL;
});
