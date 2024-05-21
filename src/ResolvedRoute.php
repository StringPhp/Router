<?php

namespace StringPhp\Router;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;

readonly class ResolvedRoute
{
    public function __construct(
        public Request $request,
        public Route $route,
        public array $parameters
    ) {
    }

    public function __invoke(): Response
    {
        return $this->route->run(
            $this->request,
            $this->parameters
        );
    }
}
