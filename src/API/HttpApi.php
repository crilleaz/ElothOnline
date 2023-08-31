<?php
declare(strict_types=1);

namespace Game\API;

use Game\Auth\AuthService;
use Game\Chat\Chat;
use Game\Engine\Error;
use Game\Client;
use Game\Item\Item;
use Game\Player\Player;
use Game\Trade\Offer;
use Game\Trade\ShopRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpApi
{
    private Player $player;

    public function __construct(private readonly Client $client, private readonly AuthService $authService)
    {

    }

    public function handle(Request $request): Response
    {
        $currentPlayer = $this->client->getCurrentPlayer();
        if ($currentPlayer === null) {
            return $this->failure('Player is not authenticated. Please, sign in first.');
        }
        $this->player = $currentPlayer;

        $action = $request->query->getString('action');
        switch ($action) {
            case 'addChatMessage':
                return $this->addChatMessage($request);
            case 'getChatMessages':
                return $this->getLastChatMessages();
            case 'ban':
                return $this->banPlayer($request);
            case 'buyItem':
                return $this->buyItem($request);
            case 'useItem':
                return $this->useItem($request);
            case 'sellItem':
                return $this->sellItem($request);
            case 'viewLogs':
                return $this->viewLogs($request);
            default:
                return $this->failure('Unknown API action');
        }
    }

    private function addChatMessage(Request $request): Response
    {
        $message = trim($request->request->getString('message'));
        if ($message === '') {
            return $this->failure('Message can not be empty');
        }

        \DI::getService(Chat::class)->addMessage($this->player, $message);

        return $this->success();
    }

    private function getLastChatMessages(): Response
    {
        $messages = \DI::getService(Chat::class)->getLastMessages(10);
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

    private function banPlayer(Request $request): Response
    {
        $username = trim($request->request->getString('username'));
        if ($username === '') {
            return $this->failure('Username is empty');
        }

        $user = $this->authService->getCurrentUser();
        if ($user === null || !$user->isAdmin()) {
            return $this->failure('Only admin can perform this action');
        }

        \DI::getService(AuthService::class)->banPlayer($username);
        \DI::getService(Chat::class)->addSystemMessage(sprintf('User "%s" has been banned', $username));

        return $this->success();
    }

    private function buyItem(Request $request): Response
    {
        $itemId = $request->request->getInt('itemId');
        $fromShop = $request->request->getString('shop');

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

    private function sellItem(Request $request): Response
    {
        $itemId = $request->request->getInt('itemId');
        $quantity = 1;

        $sellingItem = new Item($itemId, $quantity);

        $goldId = 1;

        $trade = new Offer(
            new Item($goldId, $sellingItem->worth * $quantity),
            $sellingItem
        );

        $error = $this->player->acceptOffer($trade);
        if ($error !== null) {
            return $this->failure($error->message);
        }

        return $this->success();
    }

    private function useItem(Request $request): Response
    {
        $itemId = $request->request->getInt('itemId');
        if ($itemId === 0) {
            return $this->failure('Invalid item id');
        }

        $error = $this->player->useItem($itemId);
        if ($error !== null) {
            return $this->failure($error->message);
        }

        return $this->success();
    }

    private function viewLogs(Request $request): Response
    {
        $logs = iterator_to_array($this->player->getLogs(5));

        return $this->success(['logs' => $logs]);
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
