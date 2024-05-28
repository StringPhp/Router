<?php

use Amp\Http\Server\Driver\Client;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use HttpSoft\Message\Uri;
use StringPhp\Router\Route;
use StringPhp\Router\Router;

$router = $client = null;

it('creates router and registers route', function () use (&$router, &$client) {
    $route = new Route(
        '/',
        static function (Route $route): Response {
            return new Response(body: 'Hello World');
        }
    );

    $router = new Router();
    $router->addRoute($route);
    $client = $this->createMock(Client::class);

    expect($router->getRoutes())->toBe([$route]);
});

it('creates and resolves route', function () use (&$client, &$router) {
    $request = new Request(
        $client,
        'GET',
        new Uri('/'),
    );

    $response = $router->resolveRoute($request)();

    expect($response->getBody()->read())->toBe('Hello World');
});

it('returns null for unmapped endpoint', function () use (&$client, &$router) {
    $request = new Request(
        $client,
        'GET',
        new Uri('/non-existent-route'),
    );

    expect($router->resolveRoute($request))->toBe(null);
});
