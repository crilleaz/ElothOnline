<?php
declare(strict_types=1);

namespace Game\API;

use Game\Chat\Chat;
use Game\Game;
use Game\Player\Player;
use Psr\Http\Message\ResponseInterface;

class HttpApi
{
    public function __construct(private readonly Player $player)
    {

    }

    public function addChatMessage(string $message): ResponseInterface
    {
        $message = trim($message);
        if ($message === '') {
            return $this->failure('Message can not be empty');
        }

        \DI::getService(Chat::class)->addMessage($this->player, $message);

        return $this->success();
    }

    public function getLastChatMessages(int $amountOfMessages): ResponseInterface
    {
        if ($amountOfMessages <= 0) {
            return $this->failure('Can not fetch zero or negative amount of messages');
        }

        $messages = \DI::getService(Chat::class)->getLastMessages($amountOfMessages);
        $messagesData = [];
        foreach ($messages as $message) {
            $messagesData[] = [
                'isFromAdmin' => $message->isFromAdmin,
                'sender' => $message->sender,
                'message' => $message->content,
                'sentAt' => $message->sentAt->format(DATE_ATOM),
            ];
        }

        return $this->success($messagesData);
    }

    public function banPlayer(string $username): ResponseInterface
    {
        $username = trim($username);
        if ($username === '') {
            return $this->failure('You need to pass the username to ban him');
        }

        if (!$this->player->isAdmin()) {
            return $this->failure('Sorry, you\'re not admin.');
        }

        \DI::getService(Game::class)->banPlayer($username);

        return $this->success();
    }

    private function success(array $data = []): ResponseInterface
    {
        return Response::json([
            'success' => true,
            'message' => '',
            'data' => $data,
        ]);
    }

    private function failure(string $message): Response
    {
        return Response::json([
            'success' => false,
            'message' => $message,
            'data' => [],
        ]);
    }
}
