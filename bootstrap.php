<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

if (!file_exists(__DIR__ . '/installed')) {
    exit('Please, perform installation before running the app');
}

const PROJECT_ROOT = __DIR__;