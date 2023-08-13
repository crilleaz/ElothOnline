<?php
declare(strict_types=1);

use Game\Chat\Chat;
use Game\Game;
use Game\Player\Player;

require_once __DIR__ . '/../bootstrap.php';

function sendApiResponse(array $responseData, bool $success = true): void {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'data' => $responseData]);
}

function getCurrentPlayer(): Player {
    return getService(Game::class)->findPlayer($_SESSION['username']);
}

session_start();

if (!isset($_SESSION['username'])) {
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

        getService(Chat::class)->addMessage(getCurrentPlayer(), $message);

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

        $currentUser = getCurrentPlayer();
        if (!$currentUser->isAdmin()) {
            sendApiResponse(['error' => 'Sorry, you\'re not admin.'], false);
            break;
        }

        getService(Game::class)->banPlayer($userToBan);

        sendApiResponse([]);

        break;
    default:
        throw new RuntimeException('Unknown action');
}
