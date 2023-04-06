<?php

declare(strict_types=1);

namespace Nip\Utility;

use Exception;
use Nip\Collections\Collection;
use voku\helper\ASCII;

/**
 * Class Str
 * @package Nip\Utility
 */
class Str
{
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param   string  $subject
     * @param   string  $search
     *
     * @return string
     */
    public static function after(string $subject, string $search): string
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param   string  $subject
     * @param   string  $search
     *
     * @return string
     */
    public static function afterLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, (string)$search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, $position + strlen($search));
    }


    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param   string  $subject
     * @param   string  $search
     *
     * @return string
     */
    public static function before($subject, $search)
    {
        return $search === '' ? $subject : explode($search, $subject)[0];
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param   string  $subject
     * @param   string  $search
     *
     * @return string
     */
    public static function beforeLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return static::substr($subject, 0, $pos);
    }

    /**
     * Convert a value to camel case.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function camel($value)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;
        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param   string        $haystack
     * @param   string|array  $needles
     *
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param   string  $value
     * @param   string  $cap
     *
     * @return string
     */
    public static function finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }


    /**
     * Determine if a given string matches a given pattern.
     *
     * @param   string|array  $pattern
     * @param   string        $value
     *
     * @return bool
     */
    public static function is($pattern, $value)
    {
        $patterns = Arr::wrap($pattern);

        $value = (string)$value;

        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            $pattern = (string)$pattern;

            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern == $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string is 7 bit ASCII.
     *
     * @param   string  $value
     *
     * @return bool
     */
    public static function isAscii($value): bool
    {
        return ASCII::is_ascii((string)$value);
    }

    /**
     * Determine if a given string is a valid UUID.
     *
     * @param   string  $value
     *
     * @return bool
     */
    public static function isUuid($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
    }

    /**
     * Convert a string to kebab case.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function kebab($value)
    {
        return static::snake($value, '-');
    }

    /**
     * Convert a string to snake case.
     *
     * @param   string  $value
     * @param   string  $delimiter
     *
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        $key = $value;
        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);
            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param   string  $value
     * @param   int     $limit
     * @param   string  $end
     *
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        /** @noinspection PhpComposerExtensionStubsInspection */
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Limit the number of words in a string.
     *
     * @param   string  $value
     * @param   int     $words
     * @param   string  $end
     *
     * @return string
     */
    public static function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);
        if (!isset($matches[0]) || static::length($value) === static::length($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Return the length of the given string.
     *
     * @param   string  $value
     *
     * @return int
     */
    public static function length($value)
    {
        return mb_strlen($value);
    }

    /**
     * Parse a Class(at)method style callback into class and method.
     *
     * @param   string       $callback
     * @param   string|null  $default
     *
     * @return array
     */
    public static function parseCallback($callback, $default = null)
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param   string        $haystack
     * @param   string|array  $needles
     *
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        $haystack = (string)$haystack;
        if (empty($haystack)) {
            return false;
        }
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string contains all array values.
     *
     * @param   string    $haystack
     * @param   string[]  $needles
     *
     * @return bool
     */
    public static function containsAll($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            if (!static::contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the plural form of an English word.
     *
     * @param   string  $value
     * @param   int     $count
     *
     * @return string
     * @throws Exception
     */
    public static function plural(string $value, $count = 2): string
    {
        if (function_exists('inflector')) {
            return inflector()->pluralize($value);
        }
        throw new Exception("Plural fuction needs bytic/inflector");
    }

    /**
     * Pluralize the last word of an English, studly caps case string.
     *
     * @param   string  $value
     * @param   int     $count
     *
     * @return string
     */
    public static function pluralStudly($value, $count = 2)
    {
        $parts = preg_split('/(.)(?=[A-Z])/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE);

        $lastWord = array_pop($parts);

        return implode('', $parts) . self::plural($lastWord, $count);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param   int  $length
     *
     * @return string
     * @throws Exception
     */
    public static function random($length = 16): string
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size   = $length - $len;
            $bytes  = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Repeat the given string.
     *
     * @param   string  $string
     * @param   int     $times
     *
     * @return string
     */
    public static function repeat(string $string, int $times)
    {
        return str_repeat($string, $times);
    }

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param   string  $search
     * @param   array   $replace
     * @param   string  $subject
     *
     * @return string
     */
    public static function replaceArray($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = static::replaceFirst($search, $value, $subject);
        }

        return $subject;
    }

    /**
     * Replace the given value in the given string.
     *
     * @param   string|string[]  $search
     * @param   string|string[]  $replace
     * @param   string|string[]  $subject
     *
     * @return string
     */
    public static function replace($search, $replace, $subject)
    {
        return str_replace($search, $replace, $subject);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param   string  $search
     * @param   string  $replace
     * @param   string  $subject
     *
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        $position = strpos($subject, $search);
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param   string  $search
     * @param   string  $replace
     * @param   string  $subject
     *
     * @return string
     */
    public static function replaceLast($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Remove any occurrence of the given string in the subject.
     *
     * @param   string|array<string>  $search
     * @param   string                $subject
     * @param   bool                  $caseSensitive
     *
     * @return string
     */
    public static function remove($search, $subject, $caseSensitive = true)
    {
        $subject = $caseSensitive
            ? str_replace($search, '', $subject)
            : str_ireplace($search, '', $subject);

        return $subject;
    }

    /**
     * Convert the given string to title case.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Convert the given string to title case for each word.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function headline($value)
    {
        $parts = explode('_', static::replace(' ', '_', $value));

        if (count($parts) > 1) {
            $parts = array_map([static::class, 'title'], $parts);
        }

        $studly = static::studly(implode($parts));

        $words = preg_split('/(?=[A-Z])/', $studly, -1, PREG_SPLIT_NO_EMPTY);

        return implode(' ', $words);
    }

    /**
     * Get the singular form of an English word.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function singular($value)
    {
        return Pluralizer::singular($value);
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param   string  $title
     * @param   string  $separator
     *
     * @return string
     */
    public static function slug($title, $separator = '-')
    {
        $title = static::ascii($title);
        // Convert all dashes/underscores into separator
        $flip  = $separator == '-' ? '_' : '-';
        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);
        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));
        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param   string  $value
     * @param   string  $language
     *
     * @return string
     */
    public static function ascii($value, $language = 'en')
    {
        return ASCII::to_ascii((string)$value, $language);
    }

    /**
     * Returns the replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/2.3.1/LICENSE.txt
     *
     * @return array
     */
    protected static function charsArray()
    {
        static $charsArray;
        if (isset($charsArray)) {
            return $charsArray;
        }

        return $charsArray = require dirname(__DIR__) . '/data/charsArray.php';
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param   string        $haystack
     * @param   string|array  $needles
     *
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        $haystack = (string)$haystack;
        foreach ((array)$needles as $needle) {
            if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Make a string's first character uppercase.
     *
     * @param   string  $string
     *
     * @return string
     */
    public static function ucfirst($string)
    {
        return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }

    /**
     * Get the number of words a string contains.
     *
     * @param   string  $string
     *
     * @return int
     */
    public static function wordCount($string)
    {
        return str_word_count($string);
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param   string  $value
     *
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param   string    $string
     * @param   int       $start
     * @param   int|null  $length
     *
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Returns the number of substring occurrences.
     *
     * @param   string    $haystack
     * @param   string    $needle
     * @param   int       $offset
     * @param   int|null  $length
     *
     * @return int
     */
    public static function substrCount($haystack, $needle, $offset = 0, $length = null)
    {
        if (!is_null($length)) {
            return substr_count($haystack, $needle, $offset, $length);
        } else {
            return substr_count($haystack, $needle, $offset);
        }
    }

    /**
     * @param         $data
     * @param   bool  $strict
     *
     * @return bool
     */
    public static function isSerialized($data, $strict = true)
    {
        // if it isn't a string, it isn't serialized.
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');
            // Either ; or } must exist.
            if (false === $semicolon && false === $brace) {
                return false;
            }
            // But neither must be in the first X characters.
            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }
            if (false !== $brace && $brace < 4) {
                return false;
            }
        }
        $token = $data[0];
        switch ($token) {
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
            // or else fall through
            // no break
            case 'a':
            case 'O':
                return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';

                return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }

        return false;
    }

    /**
     * @param $str
     * @param $first
     * @param $last
     *
     * @return string
     */
    public static function mask($str, $first = 0, $last = 0)
    {
        $len    = strlen($str);
        $toShow = $first + $last;

        return substr($str, 0, $len <= $toShow ? 0 : $first)
            . str_repeat("*", $len - ($len <= $toShow ? 0 : $toShow))
            . substr($str, $len - $last, $len <= $toShow ? 0 : $last);
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param   string  $pattern
     * @param   string  $subject
     *
     * @return string
     */
    public static function match($pattern, $subject)
    {
        preg_match($pattern, $subject, $matches);

        if (!$matches) {
            return '';
        }

        return $matches[1] ?? $matches[0];
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param   string  $pattern
     * @param   string  $subject
     *
     * @return Collection
     */
    public static function matchAll($pattern, $subject)
    {
        preg_match_all($pattern, $subject, $matches);

        if (empty($matches[0])) {
            return collect();
        }

        return collect($matches[1] ?? $matches[0]);
    }

    /**
     * Pad both sides of a string with another.
     *
     * @param   string  $value
     * @param   int     $length
     * @param   string  $pad
     *
     * @return string
     */
    public static function padBoth($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_BOTH);
    }

    /**
     * Pad the left side of a string with another.
     *
     * @param   string  $value
     * @param   int     $length
     * @param   string  $pad
     *
     * @return string
     */
    public static function padLeft($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_LEFT);
    }

    /**
     * Pad the right side of a string with another.
     *
     * @param   string  $value
     * @param   int     $length
     * @param   string  $pad
     *
     * @return string
     */
    public static function padRight($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_RIGHT);
    }

    /**
     * @param           $name
     * @param   string  $separator
     *
     * @return string
     */
    public static function initials($name, $separator = '.')
    {
        $name     = str_replace(['-', '_'], ' ', $name);
        $split    = explode(" ", $name);
        $split    = array_filter($split, function ($part) {
            return !empty($part);
        });
        $initials = [];
        foreach ($split as $part) {
            $initials[] = ucfirst($part[0]);
        }

        return implode($separator, $initials) . $separator;
    }

    /**
     * @param   string  $string
     * @param   bool    $return
     * @param   array   $params
     *
     * @return bool|mixed
     */
    public static function isJson(string $string, $return = false, ...$params)
    {
        $data = json_decode($string, ...$params);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return $return ? $data : true;
    }

    /**
     * Determine if the given value is a standard date format.
     *
     * @param   string  $value
     *
     * @return bool
     */
    public static function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }

    /**
     * @param $hex
     *
     * @return string
     */
    public static function fromHex($hex): string
    {
        $str = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $str .= chr(hexdec(substr($hex, $i, 2)));
        }

        return $str;
    }
}
