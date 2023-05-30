<?php

namespace Nip\Utility\Traits;

/**
 * Trait SingletonTrait
 * @package Nip\Utility\Traits
 */
trait SingletonTrait
{
    /**
     * Singleton
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof static)) {
            $instance = new static();
        }
        return $instance;
    }
}
