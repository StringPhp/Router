<?php

namespace StringPhp\Router\Attributes;

use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use StringPhp\Router\Method;

abstract class RequestAttribute
{
    abstract public static function getMethod(): Method;

    public function __construct(
        public readonly string $path,
        public readonly array $middleware = [],
        public readonly array $postware = [],
    ) {
    }

    /**
     * @param class-string $className
     *
     * @return self[]
     */
    public static function getFromClass(string $className): array
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class {$className} does not exist.");
        }

        $reflection = new ReflectionClass($className);
        $attributes = $reflection->getAttributes(static::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($attributes)) {
            return [];
        }

        return array_map(static fn ($attribute) => $attribute->newInstance(), $attributes);
    }
}
