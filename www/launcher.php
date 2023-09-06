<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

session_start();

$gameClient = DI::getService(\Game\Client::class);
$gameClient->run();
