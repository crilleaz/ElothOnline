<?php
declare(strict_types=1);

use Game\Engine\DBConnection;

final class Installer
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

    private string $currentVersion = '';

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

        $tmpLock = fopen(self::LOCK_FILE_TMP, 'w');
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
            file_put_contents(self::LOCK_FILE, $this->currentVersion);
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

        $isUpdate = [] !== $db->fetchRow("SHOW TABLES LIKE 'version'");
        if ($isUpdate) {
            $currentVersionDetails = $db->fetchRow('SELECT current from version LIMIT 1');
            if ($currentVersionDetails === []) {
                throw new RuntimeException('Version system does not contain current version information!');
            }
            $this->currentVersion = $currentVersionDetails['current'];
        }

        foreach ($dataSources as $sourceVersion => $dataSource) {
            $sourceVersion = (string)$sourceVersion;
            if ($this->isAlreadyInstalled($sourceVersion)) {
                continue;
            }
            $migration = file_get_contents($dataSource);

            // Implicit migrations break transaction and thus there is no reason to attempt performing it within
            $containsImplicitCommit = preg_match('/\b(?:ALTER TABLE|DROP TABLE|CREATE TABLE)\b/i', $migration) === 1;
            if ($containsImplicitCommit) {
                $this->runMigration($db, $sourceVersion, $migration);
            } else {
                $db->transaction(fn(DBConnection $dbConnection) => $this->runMigration($dbConnection, $sourceVersion, $migration));
            }
        }
    }

    private function dependenciesAreInstalled(): bool
    {
        return file_exists(self::FILE_LIB_LOADER);
    }

    private function isAlreadyInstalled(string $version): bool
    {
        return strnatcmp($version, $this->currentVersion) <= 0;
    }

    private function runMigration(DBConnection $db, string $version, string $migration): void
    {
        foreach (explode(';' . PHP_EOL, $migration) as $query) {
            $query = trim($query);
            if ($query !== '') {
                $db->execute($query);
            }
        }

        if ([] !== $db->fetchRow('SELECT * from version LIMIT 1')) {
            $db->execute('UPDATE version SET current=? LIMIT 1', [$version]);
        } else {
            $db->execute('INSERT INTO version(current) VALUE (?)', [$version]);
        }

        $this->currentVersion = $version;
    }
}

try {
    (new Installer())->run();
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
