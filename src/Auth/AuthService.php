<?php

declare(strict_types=1);

namespace Game\Auth;

use Game\Engine\DBConnection;
use Game\Engine\Error;
use Game\User;

class AuthService
{
    private ?User $currentUser = null;

    public function __construct(private readonly DBConnection $db)
    {
    }

    public function banPlayer(string $name): void
    {
        $this->db->execute('UPDATE users set banned = 1 WHERE anv = ?', [$name]);
    }

    public function register(string $playerName, string $password, ?string $ip = ''): null|Error
    {
        if ($playerName === '') {
            return new Error('Username can not be empty');
        }

        if (mb_strlen($password) < 8) {
            return new Error('Password must be at least 8 characters long');
        }

        if ($this->db->fetchRow('SELECT id FROM users WHERE anv=?', [$playerName]) !== []) {
            return new Error('Username is already taken');
        }

        $ip = $ip ?? $_SERVER['REMOTE_ADDR'];

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $this->db->execute('INSERT INTO users (anv, pwd, last_ip) VALUES (?, ?, ?)', [$playerName, $hashedPassword, $ip]);

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

        $this->currentUser    = null;
        $_SESSION['username'] = $user['anv'];

        return null;
    }

    public function logout(): void
    {
        session_destroy();
    }

    public function getCurrentUser(): ?User
    {
        if ($this->currentUser === null) {
            if (!isset($_SESSION['username'])) {
                return null;
            }

            $user = $this->db->fetchRow('SELECT id, anv as name, banned FROM users WHERE anv=?', [$_SESSION['username']]);
            if ($user === []) {
                // This shouldn't be possible but if this happens then it means user was deleted
                $this->logout();

                return null;
            }

            $this->currentUser = new User($user['id'], $user['name'], $user['banned'] === 1);
        }

        return $this->currentUser;
    }
}
