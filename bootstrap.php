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

final class DI
{
    private static ?ContainerInterface $container = null;

    public static function init(): void
    {
        if (self::$container !== null) {
            return ;
        }

        self::$container = new Container();
        self::$container->defaultToShared(true);
        self::$container->delegate(new ReflectionContainer(true));

        $config = require __DIR__ . '/config.php';
        self::$container->add(DBConnection::class)
            ->addArgument($config['dbHost'])
            ->addArgument($config['dbName'])
            ->addArgument($config['dbUser'])
            ->addArgument($config['dbPass']);
    }
    /**
     * @template T
     *
     * @param class-string<T> $id
     * @return T
     */
    public static function getService(string $id): object
    {
        if (self::$container === null) {
            self::init();
        }
        return self::$container->get($id);
    }
}
