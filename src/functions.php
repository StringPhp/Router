<?php

namespace StringPhp\Router;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Response;

function badRequest(array $errors): Response
{
    foreach ($errors as &$errorList) {
        if (is_string($errorList)) {
            $errorList = [$errorList];
        }
    }

    return jsonResponse(compact('errors'), HttpStatus::BAD_REQUEST);
}

function jsonResponse(mixed $data, int $status = 200): Response
{
    return new Response(
        status: $status,
        headers: ['content-type' => 'application/json'],
        body: json_encode($data)
    );
}
