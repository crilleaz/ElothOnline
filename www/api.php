<?php
declare(strict_types=1);

use Game\API\HttpApi;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../bootstrap.php';

session_start();

$api = DI::getService(HttpApi::class);

$request = Request::createFromGlobals();
$response = $api->handle($request);
$response->send();
