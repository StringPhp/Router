<?php

namespace StringPhp\Router\Middleware;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Response;
use Attribute;
use JsonException;
use Override;
use StringPhp\Router\Route;
use function StringPhp\Router\jsonResponse;

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
            return jsonResponse([
                'error' => 'Empty JSON body.'
            ], HttpStatus::BAD_REQUEST);
        }

        try {
            $json = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return jsonResponse([
                'error' => 'Invalid JSON body.',
                'message' => $e->getMessage()
            ], HttpStatus::BAD_REQUEST);
        }


        $route->setVar('body', $json);

        return $route->next();
    }
}
