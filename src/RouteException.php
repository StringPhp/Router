<?php

namespace StringPhp\Router;

use Exception;
use Throwable;

class RouteException extends Exception
{
    public function __construct(
        string $message = 'Route is already running',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
