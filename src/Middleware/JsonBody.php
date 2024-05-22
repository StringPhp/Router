<?php

namespace StringPhp\Router\Middleware;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Response;
use Attribute;
use Override;
use StringPhp\Router\Route;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class JsonBody extends MiddlewarePackage
{
    #[Override]
    public function getMiddleware(): array
    {
        return [self::handle(...)];
    }

    public static function handle(Route $route): Response
    {
        $body = $route->request->getBody()->read();

        if (empty($body)) {
            return new Response(status: HttpStatus::BAD_REQUEST, body: 'Empty body, expected JSON.');
        }

        $json = json_decode($body, true);

        if ($json === null) {
            return new Response(status: HttpStatus::BAD_REQUEST, body: 'Invalid JSON body.');
        }

        $route->setVar('body', $json);

        return$route->next();
    }
}