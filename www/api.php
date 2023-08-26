<?php
declare(strict_types=1);

use Game\API\HttpApi;
use Game\API\Response;
use Game\Game;

require_once __DIR__ . '/../bootstrap.php';

session_start();

$player = DI::getService(Game::class)->getCurrentPlayer();
if ($player === null) {
    header('Location: /login.php');
    exit();
}

$action = $_GET['action'] ?? '';
if (!is_string($action)) {
    throw new RuntimeException('Tried to perform invalid action');
}

$api = new HttpApi($player);

switch ($action) {
    case 'addChatMessage':
        $response = $api->addChatMessage((string)$_POST['message'] ?? '');
        break;
    case 'getChatMessages':
        $response = $api->getLastChatMessages(10);
        break;
    case 'ban':
        $response = $api->banPlayer((string) ($_POST['username'] ?? ''));
        break;
    case 'buyItem':
        $fromShop = (string) ($_POST['shop'] ?? '');
        $itemId = (int) ($_POST['itemId'] ?? 0);
        $response = $api->buyItem($itemId, $fromShop);
        break;
    case 'useItem':
        $itemId = (int) ($_POST['itemId'] ?? 0);
        $response = $api->useItem($itemId);
        break;
    default:
        $response = Response::json(['message' => 'Unknown API action', 'success' => false]);
}

Response::terminateWith($response);
