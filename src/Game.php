<?php
declare(strict_types=1);

namespace Game;

class Game
{
    public readonly Engine $engine;

    public readonly Wiki $wiki;

    public readonly Chat $chat;

    private static ?self $instance = null;

    private readonly DBConnection $db;

    private function __construct()
    {
        $this->db = new DBConnection("127.0.0.1", 'db', 'user', 'password');
        $this->engine = new Engine($this->db);
        $this->wiki = new Wiki($this->db);
        $this->chat = new Chat($this->db);
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

    public function banPlayer(string $name): void
    {
        $this->db->execute("UPDATE users set banned = 1 WHERE anv = ?", [$name]);

        $this->chat->addMessage(
            Player::loadPlayer('System', $this->db),
            sprintf('User "%s" has been banned', $name)
        );
    }
}