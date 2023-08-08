<?php
declare(strict_types=1);

// Generates new update file which can be used to write queries
// Updates are executed in the same order as they were created by this script

do {
    $updateFile = __DIR__ . '/' . date('YmdHis') . '.sql';
} while(file_exists($updateFile));

file_put_contents($updateFile, 'Write your SQL here' . PHP_EOL);
