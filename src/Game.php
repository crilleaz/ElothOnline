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
        $this->db = new DBConnection("127.0.0.1", 'db', 'user', 'password');
        if (!isset($_SESSION['username'])) {
            // TODO this is out of the normal flow and such state is required by a few scripts which must not be coupled with the main flow
            // currently necessary for server.php
            return;
        }
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