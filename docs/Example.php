<?php

use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use StringPhp\Router\HttpServer;
use StringPhp\Router\Route;
use StringPhp\Router\Router;

use function Amp\ByteStream\getStdout;
use function Amp\trapSignal;

require_once __DIR__ . '/../vendor/autoload.php';

$logger = new Logger('http');
$logger->pushHandler(new StreamHandler(getStdout()));

$socketServer = SocketHttpServer::createForDirectAccess($logger);
$socketServer->expose('127.0.0.1:8085');

$router = new Router();

$server = new HttpServer(
    $logger,
    $socketServer,
    $router
);

$router->get('/', function (Route $route): Response {
    return new Response(
        status: 200,
        headers: ['content-type' => 'text/html'],
        body: "<h1>Hello World!</h1>"
    );
});

$server->start();

trapSignal([SIGINT, SIGTERM]);

$server->stop();
