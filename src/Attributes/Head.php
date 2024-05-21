<?php

namespace StringPhp\Router\Attributes;

use Attribute;
use StringPhp\Router\Method;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Head extends RequestAttribute
{
    public static function getMethod(): Method
    {
        return Method::HEAD;
    }
}
