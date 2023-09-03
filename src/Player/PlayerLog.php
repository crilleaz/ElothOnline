<?php

declare(strict_types=1);

namespace Game\Player;

use Game\Engine\DBConnection;

readonly class PlayerLog
{
    public function __construct(private DBConnection $db)
    {
    }

    public function add(int $characterId, string $log): void
    {
        $this->db->execute('INSERT INTO log (character_id, message) VALUES (?, ?)', [$characterId, $log]);
    }

    /**
     * @return iterable<string>
     */
    public function readLogs(int $characterId, int $amount): iterable
    {
        $logs = $this->db
            ->fetchRows('SELECT message FROM log WHERE id=? ORDER BY tid DESC LIMIT ?', [$characterId, $amount]);

        foreach ($logs as $log) {
            yield $log['message'];
        }
    }
}
