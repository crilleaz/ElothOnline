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

        $this->chat->addSystemMessage(sprintf('User "%s" has been banned', $name));
    }

    public function register(string $playerName, string $password, ?string $ip = ''): null|Error
    {
        if ($playerName === '') {
            return new Error('Username can not be empty');
        }

        if (mb_strlen($password) < 8) {
            return new Error('Password must be at least 8 characters long');
        }

        if (Player::exists($playerName, $this->db)) {
            return new Error('Username is already taken');
        }

        $ip = $ip ?? $_SERVER['REMOTE_ADDR'];

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $this->db->execute('INSERT INTO users (anv, pwd, last_ip) VALUES (?, ?, ?)', [$playerName, $hashedPassword, $ip]);
        $this->db->execute(
            "INSERT INTO players(name, experience, health, health_max)
                            VALUES (?, '0', 15, 15)",
            [$playerName]
        );

        $this->chat->addSystemMessage('Registration: New member joined!');
        $this->engine->addToInventory(ItemId::GOLD, 10, 0, $playerName);
        $this->engine->playerLog->add(
            $playerName,
            "[System] Welcome $playerName! <br> This is your Combat log, right now its empty :( <br> Visit <a href='/?tab=dungeons'>Dungeons to start your adventure!</a>"
        );

        return null;
    }
}