<?php
declare(strict_types=1);

use Game\Engine\DBConnection;

final readonly class Installer
{
    private const DIR_LIB = __DIR__ . '/vendor';
    private const FILE_LIB_LOADER = self::DIR_LIB . '/autoload.php';
    private const DIR_DATASOURCE = __DIR__ . '/updates';
    private const LOCK_FILE = __DIR__ . '/installed';
    private const LOCK_FILE_TMP = self::LOCK_FILE . '.tmp';
    private const FILE_CONFIG = __DIR__ . '/config.php';

    /**
     * @var array{dbHost:string, dbName: string, dbUser: string, dbPass:string}
     */
    private array $config;

    public function __construct()
    {
        if (realpath(__DIR__ . '/install.php') !== __FILE__) {
            throw new RuntimeException('Attempted to perform installation from unexpected context');
        }

        $this->config = require self::FILE_CONFIG;
    }

    public function run(): void
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Installation is in progress');
        }

        if (file_exists(self::LOCK_FILE)) {
            throw new RuntimeException('Installation has already been performed');
        }

        $tmpLock = fopen(self::LOCK_FILE_TMP, 'a');
        if ($tmpLock === false) {
            throw new RuntimeException("Couldn't start installation");
        }

        if (!flock($tmpLock, LOCK_EX)) {
            fclose($tmpLock);

            throw new RuntimeException('Installation has already been performed');
        }

        try {
            $this->installDependencies();
            $this->installDatasource();
            file_put_contents(self::LOCK_FILE, 'complete');
        } finally {
            fclose($tmpLock);
            unlink(self::LOCK_FILE_TMP);
        }
    }

    private function isRunning(): bool
    {
        return file_exists(self::LOCK_FILE_TMP);
    }

    private function installDependencies(): void
    {
        if ($this->dependenciesAreInstalled()) {
            require_once self::FILE_LIB_LOADER;

            return;
        }

        $output = [];
        $resultCode = 0;
        exec('php composer.phar install --no-interaction', $output, $resultCode);
        if ($resultCode !== 0) {
            throw new RuntimeException(implode(PHP_EOL, $output));
        }

        require_once self::FILE_LIB_LOADER;
    }

    private function installDatasource(): void
    {
        if (!$this->dependenciesAreInstalled()) {
            throw new RuntimeException('Dependencies shall be installed first');
        }

        $db = new DBConnection(
            $this->config['dbHost'],
            $this->config['dbName'],
            $this->config['dbUser'],
            $this->config['dbPass']
        );

        /**
         * @var SplFileInfo $file
         */
        foreach (new DirectoryIterator(self::DIR_DATASOURCE) as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'sql') {
                continue;
            }

            $dataSources[$file->getBasename('.sql')] = $file->getRealPath();
        }

        ksort($dataSources, SORT_NATURAL);

        $db->transaction(function(DBConnection $dbConnection) use ($dataSources) {
            foreach ($dataSources as $sourceVersion => $dataSource) {
                try {
                    foreach(explode(';'. PHP_EOL, file_get_contents($dataSource)) as $query) {
                        $query = trim($query);
                        if ($query !== '') {
                            $dbConnection->execute($query);
                        }
                    }

                    if ([] !== $dbConnection->fetchRow('SELECT * from version LIMIT 1')) {
                        $dbConnection->execute('UPDATE version SET current=? LIMIT 1', [$sourceVersion]);
                    } else {
                        $dbConnection->execute('INSERT INTO version(current) VALUE (?)', [$sourceVersion]);
                    }
                } catch (Throwable $e) {
                    echo $e->getMessage() . PHP_EOL;

                    throw $e;
                }
            }
        });
    }

    private function dependenciesAreInstalled(): bool
    {
        return file_exists(self::FILE_LIB_LOADER);
    }
}

try {
    (new Installer())->run();
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}
