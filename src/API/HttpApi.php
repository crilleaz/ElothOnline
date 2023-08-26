<?php
declare(strict_types=1);

namespace Game\API;

use Game\Chat\Chat;
use Game\Engine\Error;
use Game\Game;
use Game\Player\Player;
use Game\Wiki;
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

    public function buyItem(int $itemId, string $fromShop): ResponseInterface
    {
        if ($itemId <= 0) {
            return $this->failure('Invalid item id');
        }

        $shop = \DI::getService(Wiki::class)->findShop($fromShop);
        if ($shop === null) {
            return $this->failure('Shop not found');
        }

        $offer = $shop->findOffer($itemId);
        if ($offer === null) {
            return $this->failure('Shop does not have such item');
        }

        $result = $this->player->acceptOffer($offer);
        if ($result instanceof Error) {
            return $this->failure($result->message);
        }

        return $this->success();
    }

    public function useItem(int $itemId): ResponseInterface
    {
        $error = $this->player->useItem($itemId);
        if ($error !== null) {
            return $this->failure($error->message);
        }

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
