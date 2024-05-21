<?php

namespace StringPhp\Router;

use Amp\Http\Server\Response;
use LogicException;
use StringPhp\Router\Attributes\RequestAttribute;
use StringPhp\Router\Middleware\MiddlewarePackage;

abstract class Endpoint implements RegisterRoutes
{
    abstract public static function handle(Route $route): Response;

    public static function registerRoutes(Router $router): void
    {
        $requestAttributes = RequestAttribute::getFromClass(static::class);

        if ($requestAttributes === []) {
            throw new LogicException(static::class . ' does not have any RequestAttributes.');
        }

        foreach ($requestAttributes as $requestAttribute) {
            $middleware = $requestAttribute->middleware;

            foreach (MiddlewarePackage::getFromClass(static::class) as $middlewarePackage) {
                $middleware = [...$middleware, ...$middlewarePackage->getMiddleware()];
            }

            $router->addRoute(
                new Route(
                    path: $requestAttribute->path,
                    handler: [static::class, 'handle'](...),
                    middleware: $middleware,
                    postware: $requestAttribute->postware,
                    methods: $requestAttribute->getMethod()
                )
            );
        }
    }
}
