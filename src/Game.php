<?php
declare(strict_types=1);

namespace Game;

use Game\Chat\Chat;
use Game\Engine\DBConnection;
use Game\Engine\Error;
use Game\Item\Item;
use Game\Item\ItemPrototypeRepository;
use Game\Player\Player;
use Game\Player\PlayerLog;

readonly class Game
{
    public function __construct(
        private DBConnection $db,
        private Chat $chat,
        private PlayerLog $playerLog,
        private ItemPrototypeRepository $itemPrototypeRepository
    ){}

    public function getCurrentPlayer(): ?Player
    {
        $name = $_SESSION['username'] ?? '';
        if ($name === '' || !Player::exists($name, $this->db)) {
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
        $gold = $this->itemPrototypeRepository->getById(1);

        $player = Player::loadPlayer($playerName, $this->db);
        $player->obtain(new Item($gold, 10));

        $this->chat->addSystemMessage(sprintf('Registration: New member %s joined!', $playerName));
        $this->playerLog->add(
            $playerName,
            "[System] Welcome $playerName! <br> This is your Combat log, right now its empty :( <br> Visit <a href='/?tab=dungeons'>Dungeons to start your adventure!</a>"
        );

        return null;
    }

    public function login(string $playerName, string $password): null|Error
    {
        $user = $this->db->fetchRow('SELECT * FROM users WHERE anv = ?', [$playerName]);

        // User does not exist
        if ($user === []) {
            return new Error('Invalid username or password');
        }

        // Password does not match
        if ($user['pwd'] !== $password && !password_verify($password, $user['pwd'])) {
            return new Error('Invalid username or password');
        }

        if ($user['banned'] === 1) {
            return new Error('User is banned');
        }

        $_SESSION['username'] = $user['anv'];

        return null;
    }

    public function listTopPlayers(int $amount): iterable
    {
        $topPlayers = $this->db->fetchRows('SELECT name FROM players ORDER BY level DESC LIMIT ' . $amount);

        foreach ($topPlayers as $topPlayer) {
            yield Player::loadPlayer($topPlayer['name'], $this->db);
        }
    }
}
