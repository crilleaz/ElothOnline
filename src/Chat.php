<?php
declare(strict_types=1);

namespace Game;

readonly class Chat
{
    public function __construct(private DBConnection $db) {}

    public function addMessage(Player $player, string $message): void
    {
        $this->db->execute("INSERT INTO chat (username, messages) VALUES (?, ?)", [$player->getName(), $message]);
    }

    /**
     * @param int $amount
     * @return iterable<ChatMessage>
     */
    public function getLastMessages(int $amount): iterable
    {
        // TODO Need to filter out message from banned users?
        $result = $this->db->fetchRows("SELECT * FROM chat ORDER BY id DESC LIMIT " . $amount);
        foreach ($result as $message) {
            yield new ChatMessage(
                $message['username'],
                $message['messages'],
                strtotime($message['tid']),
                $this->isFromAdmin($message['username'])
            );
        }
    }

    private function isFromAdmin(string $sender): bool
    {
        return $sender === 'System' || Player::loadPlayer($sender, $this->db)->isAdmin();
    }
}
