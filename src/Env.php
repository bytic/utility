<?php

namespace Nip\Utility;

/**
 * Class Env
 * @package Nip\Utility
 */
class Env
{
    public static function get($key, $default = null)
    {
        if ($key === 'HTTPS') {
            if (isset($_SERVER['HTTPS'])) {
                return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            }

            return strpos((string)env('SCRIPT_URI'), 'https://') === 0;
        }

        if ($key === 'SCRIPT_NAME' && env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
            $key = 'SCRIPT_URL';
        }

        $value = $default;
        if (isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $value = $_ENV[$key];
        } elseif (getenv($key) !== false) {
            $value = getenv($key);
        }

        if ($value === null) {
            return self::returnDefault($key, $default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }
//        if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
//            return substr($value, 1, -1);
//        }

        return $value;
    }

    /**
     * @param $key
     * @param $default
     *
     * @return array|bool|mixed|string|string[]
     */
    protected static function returnDefault($key, $default) {

        switch ($key) {
            case 'DOCUMENT_ROOT':
                $name = (string)env('SCRIPT_NAME');
                $filename = (string)env('SCRIPT_FILENAME');
                $offset = 0;
                if (!strpos($name, '.php')) {
                    $offset = 4;
                }

                return substr($filename, 0, -(strlen($name) + $offset));
            case 'PHP_SELF':
                return str_replace((string)env('DOCUMENT_ROOT'), '', (string)env('SCRIPT_FILENAME'));
            case 'CGI_MODE':
                return PHP_SAPI === 'cgi';
        }

        return value($default);
    }
}