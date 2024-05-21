<?php

namespace StringPhp\Router;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Closure;
use LogicException;

class Route
{
    /** @var Method[] */
    public readonly array $methods;

    private array $steps;
    private array $vars;
    private array $parameters;
    public Response $response;
    public Request $request;
    public readonly string $regexPath;

    /**
     * @param callable[] $middleware
     * @param callable[] $postware
     * @param Method[] $methods
     */
    public function __construct(
        public readonly string $path,
        Closure $handler,
        array $middleware = [],
        array $postware = [],
        array|Method $methods = Method::GET
    ) {
        $parts = explode('/', $path);

        foreach ($parts as &$part) {
            if ($part === '') {
                continue;
            }

            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $part = substr($part, 1, -1);

                $paramParts = explode(':', $part, 2);

                if (count($paramParts) === 1) {
                    $paramParts[] = '[^\/]+';
                }

                $part = '(?<' . $paramParts[0] . '>' . $paramParts[1] . ')';

                continue;
            }

            $part = preg_quote($part, '/');
        }

        $this->regexPath = '/^' . implode('\/', $parts) . '$/';

        $this->steps = [...$middleware, $handler, ...$postware];
        $this->methods = is_array($methods) ?
            array_filter($methods, static fn(mixed $method) => $method instanceof Method) :
            [$methods];
    }

    public function run(
        Request $request,
        array $parameters = []
    ): Response {
        $runner = clone $this;

        $runner->request = $request;
        $runner->parameters = $parameters;
        $response = $runner->next();
        unset($runner);

        return $response;
    }

    public function next(): Response
    {
        $step = array_shift($this->steps);

        if ($step === null) {
            throw new LogicException('No more steps to run');
        }

        return $step($this);
    }

    public function getVar(string $name): mixed
    {
        return $this->vars[$name] ?? null;
    }

    public function setVar(string $name, mixed $value): void
    {
        $this->vars[$name] = $value;
    }

    public function getParam(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }
}
