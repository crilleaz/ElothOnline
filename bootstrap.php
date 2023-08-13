<?php
declare(strict_types=1);

use Game\Engine\DBConnection;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/vendor/autoload.php';

if (!file_exists(__DIR__ . '/installed')) {
    exit('Please, perform installation before running the app');
}

const PROJECT_ROOT = __DIR__;

$di = (function (): ContainerInterface {
    $container = new Container();
    $container->defaultToShared(true);
    $container->delegate(new ReflectionContainer(true));

    $config = require PROJECT_ROOT . '/config.php';
    $container->add(DBConnection::class)
        ->addArgument($config['dbHost'])
        ->addArgument($config['dbName'])
        ->addArgument($config['dbUser'])
        ->addArgument($config['dbPass']);

    return $container;
})();

/**
 * @template T
 *
 * @param class-string<T> $id
 * @return T
 */
function getService(string $id): object
{
    global $di;

    return $di->get($id);
}
