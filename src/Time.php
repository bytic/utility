<?php

namespace Nip\Utility;

use Nip\Utility\Time\Duration;

/**
 * Class Time
 * @package Nip\Utility
 */
class Time
{
    /**
     * @param string $string
     * @return Duration
     */
    public static function fromString($string)
    {
        return new Duration($string);
    }

    /**
     * @param int $seconds
     * @return Duration
     */
    public static function fromSeconds($seconds)
    {
        return new Duration($seconds);
    }
}
