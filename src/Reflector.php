<?php

declare(strict_types=1);

namespace Nip\Utility;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

/**
 * @inspiration https://github.com/laravel/framework/blob/9.x/src/Illuminate/Support/Reflector.php
 */
class Reflector
{

    /**
     * Get the class names of the given parameter's type, including union types.
     *
     * @param   ReflectionParameter  $parameter
     *
     * @return array
     */
    public static function getParameterClassNames($parameter)
    {
        $type = $parameter->getType();

        if (!$type instanceof ReflectionUnionType) {
            return array_filter([static::getParameterClassName($parameter)]);
        }

        $unionTypes = [];

        foreach ($type->getTypes() as $listedType) {
            if (!$listedType instanceof ReflectionNamedType || $listedType->isBuiltin()) {
                continue;
            }

            $unionTypes[] = static::getTypeName($parameter, $listedType);
        }

        return array_filter($unionTypes);
    }

    /**
     * Get the class name of the given parameter's type, if possible.
     *
     * @param   ReflectionParameter  $parameter
     *
     * @return string|null
     */
    public static function getParameterClassName($parameter)
    {
        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        return static::getTypeName($parameter, $type);
    }

    /**
     * Get the given type's class name.
     *
     * @param   ReflectionParameter  $parameter
     * @param   ReflectionNamedType  $type
     *
     * @return string
     */
    protected static function getTypeName($parameter, $type)
    {
        $name  = $type->getName();
        $class = $parameter->getDeclaringClass();
        if (!is_null($class)) {
            if ($name === 'self') {
                return $class->getName();
            }

            if ($name === 'parent') {
                $parent = $class->getParentClass();
                if ($parent) {
                    return $parent->getName();
                }
            }
        }

        return $name;
    }

    /**
     * Determine if the parameter's type is a subclass of the given type.
     *
     * @param   ReflectionParameter  $parameter
     * @param   string               $className
     *
     * @return bool
     */
    public static function isParameterSubclassOf($parameter, $className)
    {
        $paramClassName = static::getParameterClassName($parameter);

        return $paramClassName
            && (class_exists($paramClassName) || interface_exists($paramClassName))
            && (new ReflectionClass($paramClassName))->isSubclassOf($className);
    }
}