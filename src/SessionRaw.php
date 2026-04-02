<?php

declare(strict_types=1);

namespace Nip\Utility;

class SessionRaw
{
    public static function read($sid)
    {
        $file = static::fileName($sid);
        if (!file_exists($file)) {
            return null;
        }
        $data = file_get_contents($file);

        return self::decode($data);
    }

    public static function fileName($sid): string
    {
        return session_save_path() . '/sess_' . $sid;
    }

    public static function decode($data): array
    {
        $backup   = $_SESSION ?? [];
        $_SESSION = [];
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_decode($data);
        $result = $_SESSION;

        $_SESSION = $backup;

        return $result;
    }
}