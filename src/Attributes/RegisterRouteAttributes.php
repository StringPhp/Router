<?php

namespace StringPhp\Router\Attributes;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use StringPhp\Router\RegisterRoutes;
use StringPhp\Router\Route;
use StringPhp\Router\Router;

class RegisterRouteAttributes implements RegisterRoutes
{
    public static function registerRoutes(Router $router): void
    {
        $reflection = new ReflectionClass(static::class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_STATIC) as $method) {
            foreach (
                $method->getAttributes(
                    RequestAttribute::class,
                    ReflectionAttribute::IS_INSTANCEOF
                ) as $requestMethodAttribute
            ) {
                /** @var RequestAttribute $requestMethod */
                $requestMethod = $requestMethodAttribute->newInstance();

                $route = new Route(
                    path: $requestMethod->path,
                    handler: [$reflection->getName(), $method->getName()](...),
                    middleware: $requestMethod->middleware,
                    postware: $requestMethod->postware,
                    methods: $requestMethod->getMethod()
                );

                $router->addRoute($route);
            }
        }
    }
}
