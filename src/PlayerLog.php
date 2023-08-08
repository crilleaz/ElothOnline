<?php
declare(strict_types=1);

namespace Game;

readonly class PlayerLog
{
    public function __construct(private DBConnection $db) {}

    public function add(string $playerName, string $log): void
    {
        $this->db->execute("INSERT INTO log (username, message) VALUES (?, ?)", [$playerName, $log]);
    }

    /**
     * @return iterable<string>
     */
    public function readLogs(string $playerName, int $amount): iterable
    {
        $logs = $this->db
            ->fetchRows("SELECT message FROM log WHERE username=? ORDER BY tid DESC LIMIT ?", [$playerName, $amount]);

        foreach ($logs as $log) {
            yield $log['message'];
        }
    }
}