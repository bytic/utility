<?php

namespace Nip\Utility;

/**
 * Class Oop
 * @package Nip\Utility
 */
class Oop
{
    /**
     * @param $class
     * @return string
     */
    public static function basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    /**
     * @param $class
     * @param bool $recursive
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

        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
            $results += static::traitUses($class);
        }

        return array_unique($results);
    }

    /**
     * @param $trait
     * @param bool $recursive
     * @return array
     */
    public static function traitUses($trait, $recursive = true)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += static::traitUses($trait);
        }

        return $traits;
    }

    /**
     * @param $name
     * @return bool
     */
    public static function isTrait($name)
    {
        return trait_exists($name);
    }
}
