<?php

namespace Nip\Utility;

/**
 * Class Number
 * @package Nip\Utility
 */
class Number
{
    public static function trimZeros($num)
    {
        return $num + 0;
    }

    public static function intOrNull($value): ?int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_null($value)) {
            return null;
        }

        return intval($value);
    }

    public static function floatOrNull($value): ?float
    {
        if (is_float($value)) {
            return $value;
        }
        if (is_null($value)) {
            return null;
        }

        return floatval($value);
    }
}
