<?php
declare(strict_types=1);

namespace Game;

class Game
{
    private static ?self $instance = null;

    private Player $currentPlayer;

    private readonly DBConnection $db;

    private function __construct()
    {
        // TODO replace with the composer autoloader
        foreach (new \DirectoryIterator(__DIR__) as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                require_once $file->getRealPath();
            }
        }
        $this->db = new DBConnection("127.0.0.1", 'db', 'user', 'password');

        $this->currentPlayer = Player::loadCurrentPlayer($this->db);
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function findPlayer(string $name): ?Player
    {
        if (!Player::exists($name, $this->db)) {
            return null;
        }

        return Player::loadPlayer($name, $this->db);
    }
}