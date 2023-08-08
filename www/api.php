<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

function sendApiResponse(array $responseData, bool $success = true): void {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'data' => $responseData]);
}

function getCurrentPlayer(): \Game\Player {
    return \Game\Game::instance()->findPlayer($_SESSION['username']);
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

        \Game\Game::instance()->chat->addMessage(getCurrentPlayer(), $message);

        sendApiResponse([]);

        break;
    case 'getChatMessages':
        $maxMessagesToShow = 10;
        $messages = \Game\Game::instance()->chat->getLastMessages($maxMessagesToShow);
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

        \Game\Game::instance()->banPlayer($userToBan);

        sendApiResponse([]);

        break;
    default:
        throw new RuntimeException('Unknown ');
}
