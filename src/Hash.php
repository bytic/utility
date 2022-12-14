<?php

namespace Nip\Utility;

class Hash
{
    public const CRC32C = 'crc32c';
    public const MD4 = 'md4';

    public static function array(array $data): string
    {
        return self::forString(serialize($data));
    }

    public static function secretToken($data): string
    {
        $data = is_string($data) ? $data : serialize($data);

        return static::string($data, self::MD4);
    }

    public static function string($data, $algo = null): string
    {
        $algo = $algo ?: self::CRC32C;

        return hash($algo, $data);
    }
}