<?php

declare(strict_types=1);

namespace Nip\Utility;

use Nip\Utility\Oop\ClassFileLocator;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use SplFileInfo;

/**
 * Class Oop
 * @package Nip\Utility
 */
class Oop
{
    /**
     * @param $class
     *
     * @return string
     * @throws ReflectionException
     */
    public static function namespace($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        $reflection_class = new ReflectionClass($class);

        return $reflection_class->getNamespaceName();
    }

    /**
     * @param   string  $namespace
     *
     * @return string
     */
    public static function namespaceBase($namespace): string
    {
        $parts = explode('\\', $namespace);
        array_pop($parts);

        return implode('\\', $parts);
    }

    /**
     * @param $class
     *
     * @return string
     */
    public static function basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    /**
     * @param $class
     * @param $trait
     *
     * @return bool
     */
    public static function classUsesTrait($class, $trait): bool
    {
        $traits = static::uses($class);

        return isset($traits[$trait]);
    }

    /**
     * @param         $class
     * @param   bool  $recursive
     *
     * @return array
     */
    public static function uses($class, $recursive = true)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        if (class_exists($class) && $recursive == false) {
            return class_uses($class);
        }
        $results = [];

        $classParents = class_parents($class);
        $classParents = is_array($classParents) ? array_reverse($classParents) : [];
        foreach ($classParents + [$class => $class] as $class) {
            $results += static::traitUses($class);
        }

        return array_unique($results);
    }

    /**
     * @param         $trait
     * @param   bool  $recursive
     *
     * @return array
     */
    public static function traitUses($trait, $recursive = true)
    {
        $traits = class_uses($trait);
        $traits = is_array($traits) ? $traits : [];

        foreach ($traits as $trait) {
            $traits += static::traitUses($trait);
        }

        return $traits;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public static function isTrait($name)
    {
        return trait_exists($name);
    }

    /**
     * @param   SplFileInfo  $file
     *
     * @return array
     */
    public static function classesInFile(SplFileInfo $file)
    {
        return ClassFileLocator::classes($file);
    }

    /**
     * @param $object
     * @param $property
     *
     * @return bool
     * @throws ReflectionException
     */
    public static function propertyIsInitialized($object, $property): bool
    {
        $reflection = self::reflectionProperty($object, $property);

        return $reflection->isInitialized($object);
    }

    /**
     * @param $class
     * @param $property
     *
     * @return mixed|ReflectionProperty
     * @throws ReflectionException
     */
    protected static function reflectionProperty($class, $property): mixed
    {
        static $properties = [];

        $class = is_object($class) ? get_class($class) : $class;
        if (!isset($properties[$class])) {
            $properties[$class] = new ReflectionProperty($class, $property);
        }

        return $properties[$class];
    }
}
