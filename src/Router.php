<?php

namespace StringPhp\Router;

use Amp\Http\HttpStatus;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpErrorException;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Closure;

class Router
{
    /** @var Route[] */
    protected array $routes = [];

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function get(
        string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
    ): Route {
        $route = new Route($path, $handler, $middleware, $postware, Method::GET);
        $this->addRoute($route);

        return $route;
    }

    public function post(
        string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
    ): Route {
        $route = new Route($path, $handler, $middleware, $postware, Method::POST);
        $this->addRoute($route);

        return $route;
    }

    public function put(
        string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
    ): Route {
        $route = new Route($path, $handler, $middleware, $postware, Method::PUT);
        $this->addRoute($route);

        return $route;
    }

    public function patch(
        string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
    ): Route {
        $route = new Route($path, $handler, $middleware, $postware, Method::PATCH);
        $this->addRoute($route);

        return $route;
    }

    public function delete(
        string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
    ): Route {
        $route = new Route($path, $handler, $middleware, $postware, Method::DELETE);
        $this->addRoute($route);

        return $route;
    }

    public function head(
        string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
    ): Route {
        $route = new Route($path, $handler, $middleware, $postware, Method::HEAD);
        $this->addRoute($route);

        return $route;
    }

    public function any(
        string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
    ): Route {
        $route = new Route($path, $handler, $middleware, $postware, [...Method::cases()]);
        $this->addRoute($route);

        return $route;
    }

    public function resolveRoute(
        Request $request
    ): ?ResolvedRoute {
        foreach ($this->routes as $route) {
            if (!in_array(Method::from($request->getMethod()), $route->methods, true)) {
                continue;
            }

            if (!preg_match($route->regexPath, $request->getUri()->getPath(), $parameters)) {
                continue;
            }

            return new ResolvedRoute(
                $request,
                $route,
                $parameters
            );
        }

        return null;
    }
}
