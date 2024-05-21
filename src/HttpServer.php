<?php

namespace StringPhp\Router;

use Amp\Http\HttpStatus;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpErrorException;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use function StringPhp\Utils\getClasses;

class HttpServer implements RequestHandler, ErrorHandler
{
    public readonly Router $router;
    public function __construct(
        public readonly LoggerInterface $logger,
        public readonly SocketHttpServer $server,
        ?Router $router = null
    ) {
        $this->router = $router ?? new Router();
    }

    public function registerRoutes(RegisterRoutes $registerRoutes): void
    {
        $registerRoutes->registerRoutes($this->router);
    }

    public function autoloadRoutesInDirectory(string $directory, string $namespace, bool $recursive = true, array $ignoreDirectories = []): void
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException("Directory {$directory} does not exist or is not a directory");
        }

        foreach (getClasses($directory, $namespace, $recursive, $ignoreDirectories) as [$className, $namespace, $fullClassName, $directory]) {
            $reflection = new ReflectionClass($fullClassName);

            if (
                !$reflection->isInstantiable() ||
                !$reflection->implementsInterface(RegisterRoutes::class)
            ) {
                continue;
            }

            $this->registerRoutes(new $fullClassName());
        }
    }

    public function start(): void
    {
        $this->server->start($this, $this);
    }

    public function stop(): void
    {
        $this->server->stop();
    }

    public function handleRequest(Request $request): Response
    {
        $resolvedRoute = $this->router->resolveRoute($request);

        if ($resolvedRoute === null) {
            throw new HttpErrorException(
                status: HttpStatus::NOT_FOUND,
                reason: HttpStatus::getReason(HttpStatus::NOT_FOUND)
            );
        }

        return $resolvedRoute();
    }

    public function handleError(int $status, ?string $reason = null, ?Request $request = null): Response
    {
        return new Response(
            status: $status
        );
    }
}