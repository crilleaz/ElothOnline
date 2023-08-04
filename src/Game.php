<?php
declare(strict_types=1);

namespace Game;

class Game
{
    private static ?self $instance = null;

    private readonly DBConnection $db;

    public readonly Engine $engine;

    private function __construct()
    {
        $this->db = new DBConnection("127.0.0.1", 'db', 'user', 'password');
        $this->engine = new Engine($this->db);
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