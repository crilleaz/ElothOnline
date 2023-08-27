<?php
declare(strict_types=1);

use Game\API\HttpApi;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../bootstrap.php';

session_start();

$api = DI::getService(HttpApi::class);

$request = Request::createFromGlobals();
// decode json data and replace request params with it
$request->request = new InputBag($request->toArray());
$response = $api->handle($request);
$response->prepare($request);
$response->send();
