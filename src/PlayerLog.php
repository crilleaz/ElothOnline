<?php
declare(strict_types=1);

namespace Game;

class PlayerLog
{
    public function __construct(private readonly DBConnection $db) {}

    public function add(string $playerName, string $log): void
    {
        $this->db->execute("INSERT INTO log (username, message) VALUES ('$playerName', '$log')");
    }
}