<?php
declare(strict_types=1);

use Game\Chat\Chat;
use Game\Game;

require_once __DIR__ . '/../bootstrap.php';

function sendApiResponse(array $responseData, bool $success = true): void {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'data' => $responseData]);
}

session_start();

$player = getService(Game::class)->getCurrentPlayer();
if ($player === null) {
    header('Location: /login.php');
    exit();
}

$action = $_GET['action'] ?? '';
if (!is_string($action)) {
    throw new RuntimeException('Tried to perform invalid action');
}

switch ($action) {
    case 'addChatMessage':
        $message = (string)$_POST['message'] ?? '';
        if ($message === '') {
            sendApiResponse(['error' => 'Message can not be empty'], false);
            break;
        }

        getService(Chat::class)->addMessage($player, $message);

        sendApiResponse([]);

        break;
    case 'getChatMessages':
        $maxMessagesToShow = 10;
        $messages = getService(Chat::class)->getLastMessages($maxMessagesToShow);
        $responseData = [];
        foreach ($messages as $message) {
            $responseData[] = [
                'isFromAdmin' => $message->isFromAdmin,
                'sender' => $message->sender,
                'message' => $message->content,
                'sentAt' => $message->sentAt->format(DATE_ATOM),
            ];
        }
        sendApiResponse($responseData);
        break;
    case 'ban':
        $userToBan = $_POST['username'] ?? '';
        if ($userToBan === '') {
            sendApiResponse(['error' => 'You need to pass the username to ban him'], false);
            break;
        }

        if (!$player->isAdmin()) {
            sendApiResponse(['error' => 'Sorry, you\'re not admin.'], false);
            break;
        }

        getService(Game::class)->banPlayer($userToBan);

        sendApiResponse([]);

        break;
    default:
        throw new RuntimeException('Unknown action');
}
