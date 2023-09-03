<?php

declare(strict_types=1);

namespace Game\Player;

use Game\Engine\DBConnection;
use Game\User;

readonly class CharacterRepository
{
    public function __construct(private DBConnection $db)
    {
    }

    public function getByUser(User $user): Player
    {
        $character = $this->findByUser($user);
        if ($character === null) {
            throw new \RuntimeException('User does not have characters');
        }

        return $character;
    }

    public function findByUser(User $user): ?Player
    {
        $data = $this->db->fetchRow('SELECT id FROM players WHERE user_id = ' . $user->id);
        if ($data === []) {
            return null;
        }

        return new Player($data['id'], $this->db);
    }

    public function getByName(string $characterName): Player
    {
        $data = $this->db->fetchRow('SELECT id FROM players  WHERE name = ?', [$characterName]);
        if ($data === []) {
            throw new \RuntimeException('Character does not exist');
        }

        return new Player($data['id'], $this->db);
    }

    public function getById(int $id): Player
    {
        $data = $this->db->fetchRow('SELECT id FROM players WHERE id= ' . $id);
        if ($data === []) {
            throw new \RuntimeException('Character does not exist');
        }

        return new Player($id, $this->db);
    }

    /**
     * @param  int $amount
     * @return iterable<Player>
     */
    public function listTopCharacters(int $amount): iterable
    {
        $topCharacters = $this->db->fetchRows('SELECT id FROM players ORDER BY level DESC LIMIT ' . $amount);
        foreach ($topCharacters as $topCharacter) {
            yield new Player($topCharacter['id'], $this->db);
        }
    }
}
