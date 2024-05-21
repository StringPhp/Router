<?php

namespace StringPhp\Router\Middleware;

use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;

abstract readonly class MiddlewarePackage
{
    /**
     * @return callable[]
     */
    abstract public function getMiddleware(): array;

    /**
     * @param class-string $class
     *
     * @throws InvalidArgumentException If the class provided does not exist
     *
     * @return MiddlewarePackage[]
     */
    public static function getFromClass(string $class): array
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class {$class} does not exist");
        }

        $reflection = new ReflectionClass($class);
        $middlewarePackageAttributes = $reflection->getAttributes(static::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($middlewarePackageAttributes)) {
            return [];
        }

        return array_map(
            static fn (ReflectionAttribute $attribute): static => $attribute->newInstance(),
            $middlewarePackageAttributes
        );
    }
}
