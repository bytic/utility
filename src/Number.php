<?php

namespace Nip\Utility;

use InvalidArgumentException;

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

    /**
     * @param $value
     *
     * @return int|null
     */
    public static function intVal($value): ?int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_null($value)) {
            return null;
        }
        $value    = trim($value);
        $valueInt = intval($value);
        if ($valueInt == $value) {
            return $valueInt;
        }
        throw new InvalidArgumentException("Invalid integer value: " . $value);
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
