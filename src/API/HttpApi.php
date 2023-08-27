<?php
declare(strict_types=1);

namespace Game\API;

use Game\Chat\Chat;
use Game\Engine\Error;
use Game\Game;
use Game\Player\Player;
use Game\Trade\ShopRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpApi
{
    private Player $player;

    public function __construct(private readonly Game $game)
    {

    }

    public function handle(Request $request): Response
    {
        $currentPlayer = $this->game->getCurrentPlayer();
        if ($currentPlayer === null) {
            return $this->failure('Player is not authenticated. Please, sign in first.');
        }
        $this->player = $currentPlayer;

        $action = $request->query->getString('action');
        switch ($action) {
            case 'addChatMessage':
                return $this->addChatMessage((string)$_POST['message'] ?? '');
            case 'getChatMessages':
                return $this->getLastChatMessages(10);
            case 'ban':
                return $this->banPlayer((string) ($_POST['username'] ?? ''));
            case 'buyItem':
                $fromShop = (string) ($_POST['shop'] ?? '');
                $itemId = (int) ($_POST['itemId'] ?? 0);
                return $this->buyItem($itemId, $fromShop);
            case 'useItem':
                $itemId = (int) ($_POST['itemId'] ?? 0);
                return $this->useItem($itemId);
            default:
                return new JsonResponse(['message' => 'Unknown API action', 'success' => false]);
        }
    }

    private function addChatMessage(string $message): Response
    {
        $message = trim($message);
        if ($message === '') {
            return $this->failure('Message can not be empty');
        }

        \DI::getService(Chat::class)->addMessage($this->player, $message);

        return $this->success();
    }

    private function getLastChatMessages(int $amountOfMessages): Response
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

    private function banPlayer(string $username): Response
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

    private function buyItem(int $itemId, string $fromShop): Response
    {
        if ($itemId <= 0) {
            return $this->failure('Invalid item id');
        }

        $shop = \DI::getService(ShopRepository::class)->findShopByName($fromShop);
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

    private function useItem(int $itemId): Response
    {
        $error = $this->player->useItem($itemId);
        if ($error !== null) {
            return $this->failure($error->message);
        }

        return $this->success();
    }

    private function success(array $data = []): Response
    {
        return new JsonResponse([
            'success' => true,
            'message' => '',
            'data' => $data,
        ]);
    }

    private function failure(string $message): Response
    {
        return new JsonResponse([
            'success' => false,
            'message' => $message,
            'data' => [],
        ]);
    }
}
