<?php

namespace Nip\Utility;

use Exception;
use Psr\Container\ContainerInterface;
use Nip\Container\Container as NipContainer;

/**
 * Class Container
 * @package Nip\Utility
 */
class Container
{
    /**
     * @return false|ContainerInterface|NipContainer
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function container()
    {
        static $instance;
        if (!($instance instanceof ContainerInterface)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $instance = static::detect();
        }
        return $instance;
    }

    /**
     * @return ContainerInterface
     * @throws Exception
     */
    public static function detect()
    {
        if (class_exists(NipContainer::class)) {
            return NipContainer::getInstance();
        }
        throw new Exception("No valid container found");
    }

    /**
     * @param null $make
     * @param array $parameters
     * @return false|ContainerInterface
     */
    public static function get($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return static::container();
        }

        return static::container()->get($make, $parameters);
    }
}
