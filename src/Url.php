<?php

/**
 * @inspiration
 * https://github.com/drupal/drupal/blob/8.8.x/core/lib/Drupal/Component/Utility/UrlHelper.php
 */

namespace Nip\Utility;

/**
 * Class Url
 * @package Nip\Utility
 */
class Url
{
    /**
     * The list of allowed protocols.
     *
     * @var array
     */
    protected static $allowedProtocols = ['http', 'https'];

    /**
     * Parses an array into a valid, rawurlencoded query string.
     *
     *
     * rawurlencode() is RFC3986 compliant, and as a consequence RFC3987
     * compliant. The latter defines the required format of "URLs" in HTML5.
     * urlencode() is almost the same as rawurlencode(), except that it encodes
     * spaces as "+" instead of "%20". This makes its result non compliant to
     * RFC3986 and as a consequence non compliant to RFC3987 and as a consequence
     * not valid as a "URL" in HTML5.
     *
     * @param array $query
     *   The query parameter array to be processed,
     *   e.g. \Drupal::request()->query->all().
     * @param string $parent
     *   Internal use only. Used to build the $query array key for nested items.
     *
     * @return string
     *   A rawurlencoded string which can be used as or appended to the URL query
     *   string.
     *
     * @ingroup php_wrappers
     * @todo Remove this function once PHP 5.4 is required as we can use just
     *   http_build_query() directly.
     *
     */
    public static function buildQuery(array $query, $parent = '')
    {
        $params = [];

        foreach ($query as $key => $value) {
            $key = ($parent ? $parent . '[' . rawurlencode($key) . ']' : rawurlencode($key));

            // Recurse into children.
            if (is_array($value)) {
                $params[] = static::buildQuery($value, $key);
            } // If a query parameter value is NULL, only append its key.
            elseif (!isset($value)) {
                $params[] = $key;
            } else {
                // For better readability of paths in query strings, we decode slashes.
                $params[] = $key . '=' . str_replace('%2F', '/', rawurlencode($value));
            }
        }

        return implode('&', $params);
    }

    /**
     * Reverse of the PHP built-in function parse_url
     *
     * @see http://php.net/parse_url
     * @param $url
     * @return string
     */
    public static function build($url)
    {
        $scheme = isset($url['scheme']) ? $url['scheme'] . '://' : '';
        $host = isset($url['host']) ? $url['host'] : '';
        $port = isset($url['port']) ? ':' . $url['port'] : '';
        $user = isset($url['user']) ? $url['user'] : '';
        $pass = isset($url['pass']) ? ':' . $url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($url['path']) ? $url['path'] : '';
        $query = isset($url['query']) && $url['query'] ? '?' . $url['query'] : '';
        $fragment = isset($url['fragment']) ? '#' . $url['fragment'] : '';

        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    /**
     * Verifies the syntax of the given URL.
     *
     * This function should only be used on actual URLs. It should not be used for
     * Drupal menu paths, which can contain arbitrary characters.
     * Valid values per RFC 3986.
     *
     * @param string $url
     *   The URL to verify.
     * @param bool $absolute
     *   Whether the URL is absolute (beginning with a scheme such as "http:").
     *
     * @return bool
     *   TRUE if the URL is in a valid format, FALSE otherwise.
     */
    public static function isValid($url, $absolute = true)
    {
        if ($absolute) {
            return (bool)preg_match(
                "
        /^                                                      # Start at the beginning of the text
        (?:ftp|https?|feed):\/\/                                # Look for ftp, http, https or feed schemes
        (?:                                                     # Userinfo (optional) which is typically
          (?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*      # a username or a username and password
          (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@          # combination
        )?
        (?:
          (?:[a-z0-9\-\.]|%[0-9a-f]{2})+                        # A domain name or a IPv4 address
          |(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\])         # or a well formed IPv6 address
        )
        (?::[0-9]+)?                                            # Server port number (optional)
        (?:[\/|\?]
          (?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})   # The path and query (optional)
        *)?
      $/xi",
                $url
            );
        } else {
            return (bool)preg_match("/^(?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})+$/i", $url);
        }
    }
}
