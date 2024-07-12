<?php

/**
 * @inspiration
 * https://github.com/drupal/drupal/blob/8.8.x/core/lib/Drupal/Component/Utility/UrlHelper.php
 * https://github.com/JBZoo/Utils/blob/master/src/Url.php
 */

namespace Nip\Utility;

/**
 * Class Url
 * @package Nip\Utility
 */
class Url
{

    public const ARG_SEPARATOR = '&';

    /**
     * URL constants as defined in the PHP Manual under "Constants usable with
     * http_build_url()".
     *
     * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
     */
    public const URL_REPLACE = 1;
    public const URL_JOIN_PATH = 2;
    public const URL_JOIN_QUERY = 4;
    public const URL_STRIP_USER = 8;
    public const URL_STRIP_PASS = 16;
    public const URL_STRIP_AUTH = 32;
    public const URL_STRIP_PORT = 64;
    public const URL_STRIP_PATH = 128;
    public const URL_STRIP_QUERY = 256;
    public const URL_STRIP_FRAGMENT = 512;
    public const URL_STRIP_ALL = 1024;

    public const PORT_HTTP = 80;
    public const PORT_HTTPS = 443;

    /**
     * The list of allowed protocols.
     *
     * @var array
     */
    protected static $allowedProtocols = ['http', 'https'];

    /**
     * @param   string  $uri
     * @param   array   $args
     * @param           $subject
     *
     * @return string
     */
    public static function copyQuery(string $uri, array $args, $subject)
    {
        $params = parse_url($uri);
        parse_str($params['query'] ?? '', $params['query']);
        foreach ($args as $arg) {
            $params['query'][$arg] = isset($params['query'][$arg]) ? $params['query'][$arg] : $subject->get($arg);
        }

        return static::create($params);
    }

    public static function addArg(array $newParams, ?string $uri = null): string
    {
        $uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '');

        // Parse the URI into it's components
        $parsedUri = parse_url($uri);

        if (isset($parsedUri['query'])) {
            parse_str($parsedUri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);
        } elseif (isset($parsedUri['path']) && strstr($parsedUri['path'], '=') !== false) {
            $parsedUri['query'] = $parsedUri['path'];
            unset($parsedUri['path']);
            parse_str($parsedUri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);
        } else {
            $queryParams = $newParams;
        }

        // Strip out any query params that are set to false.
        // Properly handle valueless parameters.
        foreach ($queryParams as $param => $value) {
            if ($value === false) {
                unset($queryParams[$param]);
            } elseif ($value === null) {
                $queryParams[$param] = '';
            }
        }

        // Re-construct the query string
        $parsedUri['query'] = http_build_query($queryParams);

        // Re-construct the entire URL
        $newUri = static::build($parsedUri);


        // Make the URI consistent with our input
        if ($newUri[0] === '/' && strstr($uri, '/') === false) {
            $newUri = substr($newUri, 1);
        }

        if ($newUri[0] === '?' && strstr($uri, '?') === false) {
            $newUri = substr($newUri, 1);
        }

        return rtrim($newUri, '?');
    }

    /**
     * Removes an item or list from the query string.
     *
     * @param   string|array  $keys  Query key or keys to remove.
     * @param   string|null   $uri   When null uses the $_SERVER value
     *
     * @return string
     */
    public static function delArg($keys, ?string $uri = null): string
    {
        if (is_array($keys)) {
            $params = array_combine($keys, array_fill(0, count($keys), false)) ?: [];

            return self::addArg($params, (string)$uri);
        }

        return self::addArg([$keys => false], (string)$uri);
    }

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
     * @param   array   $query
     *   The query parameter array to be processed,
     *   e.g. \Drupal::request()->query->all().
     * @param   string  $parent
     *   Internal use only. Used to build the $query array key for nested items.
     *
     * @return string
     *   A rawurlencoded string which can be used as or appended to the URL query
     *   string.
     *
     * @ingroup php_wrappers
     * @todo    Remove this function once PHP 5.4 is required as we can use just
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
     *
     * @param $url
     *
     * @return string
     */
    public static function build($url, $parts = [], $flags = self::URL_REPLACE, &$new_url = [])
    {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);

        isset($url['query']) && is_string($url['query']) || $url['query'] = null;
        isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;

        $keys = ['user', 'pass', 'port', 'path', 'query', 'fragment'];
        // URL_STRIP_ALL and URL_STRIP_AUTH cover several other flags.
        if ($flags & self::URL_STRIP_ALL) {
            $flags |= self::URL_STRIP_USER | self::URL_STRIP_PASS
                | self::URL_STRIP_PORT | self::URL_STRIP_PATH
                | self::URL_STRIP_QUERY | self::URL_STRIP_FRAGMENT;
        } elseif ($flags & self::URL_STRIP_AUTH) {
            $flags |= self::URL_STRIP_USER | self::URL_STRIP_PASS;
        }

        // Schema and host are alwasy replaced
        foreach (['scheme', 'host'] as $part) {
            if (isset($parts[$part])) {
                $url[$part] = $parts[$part];
            }
        }

        if ($flags & self::URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $url[$key] = $parts[$key];
                }
            }
        } else {
            if (isset($parts['path']) && ($flags & self::URL_JOIN_PATH)) {
                if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
                    $url['path'] = rtrim(
                            str_replace(basename($url['path']), '', $url['path']),
                            '/'
                        ) . '/' . ltrim($parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }

            if (isset($parts['query']) && ($flags & self::URL_JOIN_QUERY)) {
                if (isset($url['query'])) {
                    parse_str($url['query'], $url_query);
                    parse_str($parts['query'], $parts_query);

                    $url['query'] = http_build_query(
                        array_replace_recursive(
                            $url_query,
                            $parts_query
                        )
                    );
                } else {
                    $url['query'] = $parts['query'];
                }
            }
        }

        if (isset($url['path']) && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }

        foreach ($keys as $key) {
            $strip = 'URL_STRIP_' . strtoupper($key);
            if ($flags & constant(Url::class . '::' . $strip)) {
                unset($url[$key]);
            }
        }

        if (isset($url['port'])) {
            $url['port'] = intval($url['port']);
            if ($url['port'] === self::PORT_HTTPS) {
                $url['scheme'] = 'https';
            } elseif ($url['port'] === self::PORT_HTTP) {
                $url['scheme'] = 'http';
            }
        }

        $parsed_string = '';

        if (isset($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }

        if (isset($url['user']) && !empty($url['user'])) {
            $parsed_string .= $url['user'];

            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }

            $parsed_string .= '@';
        }

        if (isset($url['host'])) {
            $parsed_string .= $url['host'];
        }

        if (isset($url['port']) && $url['port'] !== self::PORT_HTTP && $url['scheme'] === 'http') {
            $parsed_string .= ':' . $url['port'];
        }

        if (!empty($url['path'])) {
            $parsed_string .= $url['path'];
        } else {
            $parsed_string .= '/';
        }

        if (isset($url['query']) && !empty($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }

        if (isset($url['fragment'])) {
            $parsed_string .= '#' . trim($url['fragment'], '#');
        }

        $new_url = $url;

        return $parsed_string;
    }

    /**
     * Create URL from array params
     *
     * @param   array  $parts
     *
     * @return string
     */
    public static function create(array $parts = []): string
    {
        $parts = array_merge(
            [
                'scheme' => 'https',
                'query'  => [],
            ],
            $parts
        );

        if (is_array($parts['query'])) {
            $parts['query'] = self::buildQuery($parts['query']);
        }

        /** @noinspection ArgumentEqualsDefaultValueInspection */
        return self::build('', $parts, self::URL_REPLACE);
    }

    /**
     * Parses a URL string into its path, query, and fragment components.
     *
     * This function splits both internal paths like @code node?b=c#d @endcode and
     * external URLs like @code https://example.com/a?b=c#d @endcode into their
     * component parts. See
     * @link    http://tools.ietf.org/html/rfc3986#section-3 RFC 3986 @endlink for an
     * explanation of what the component parts are.
     *
     * Note that, unlike the RFC, when passed an external URL, this function
     * groups the scheme, authority, and path together into the path component.
     *
     * @param   string  $url
     *   The internal path or external URL string to parse.
     *
     * @return array
     *   An associative array containing:
     *   - path: The path component of $url. If $url is an external URL, this
     *     includes the scheme, authority, and path.
     *   - query: An array of query parameters from $url, if they exist.
     *   - fragment: The fragment component from $url, if it exists.
     *
     * @see     \Drupal\Core\Utility\LinkGenerator
     * @see     http://tools.ietf.org/html/rfc3986
     *
     * @ingroup php_wrappers
     */
    public static function parse($url)
    {
        $options = [
            'path'     => null,
            'query'    => [],
            'fragment' => '',
        ];

        // External URLs: not using parse_url() here, so we do not have to rebuild
        // the scheme, host, and path without having any use for it.
        // The URL is considered external if it contains the '://' delimiter. Since
        // a URL can also be passed as a query argument, we check if this delimiter
        // appears in front of the '?' query argument delimiter.
        $scheme_delimiter_position = strpos($url, '://');
        $query_delimiter_position  = strpos($url, '?');
        if ($scheme_delimiter_position !== false && ($query_delimiter_position === false || $scheme_delimiter_position < $query_delimiter_position)) {
            // Split off the fragment, if any.
            if (strpos($url, '#') !== false) {
                list($url, $options['fragment']) = explode('#', $url, 2);
            }

            // Split off everything before the query string into 'path'.
            $parts = explode('?', $url, 2);

            // Don't support URLs without a path, like 'http://'.
            list(, $path) = explode('://', $parts[0], 2);
            if ($path != '') {
                $options['path'] = $parts[0];
            }
            // If there is a query string, transform it into keyed query parameters.
            if (isset($parts[1])) {
                parse_str($parts[1], $options['query']);
            }
        } // Internal URLs.
        else {
            // parse_url() does not support relative URLs, so make it absolute. For
            // instance, the relative URL "foo/bar:1" isn't properly parsed.
            $parts = parse_url('http://example.com/' . $url);
            // Strip the leading slash that was just added.
            $options['path'] = substr($parts['path'], 1);
            if (isset($parts['query'])) {
                parse_str($parts['query'], $options['query']);
            }
            if (isset($parts['fragment'])) {
                $options['fragment'] = $parts['fragment'];
            }
        }

        return $options;
    }

    /**
     * Verifies the syntax of the given URL.
     *
     * This function should only be used on actual URLs. It should not be used for
     * Drupal menu paths, which can contain arbitrary characters.
     * Valid values per RFC 3986.
     *
     * @param   string  $url
     *   The URL to verify.
     * @param   bool    $absolute
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

    public static function getHostPart($url)
    {
        $host = parse_url($url, PHP_URL_HOST);

        return $host;
    }

    public static function getBaseHostPart($url)
    {
        $fullhost = static::getHostPart($url);

        $parts = explode(".", $fullhost);
        $tld   = array_pop($parts);
        $host  = array_pop($parts);
        if (strlen($tld) == 2 && strlen($host) <= 3) {
            $tld  = "$host.$tld";
            $host = array_pop($parts);
        }

        return "$host.$tld";
    }

    /**
     * Is absolute url
     *
     * @param   string  $path
     *
     * @return bool
     */
    public static function isAbsolute(string $path): bool
    {
        return strpos($path, '//') === 0 || preg_match('#^[a-z-]{3,}:\/\/#i', $path);
    }

    /**
     * Turns all of the links in a string into HTML links.
     *
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param   string  $text  The string to parse
     *
     * @return string
     */
    public static function linkify($text)
    {
        // IE does not handle &apos; entity!
        $text = (string)preg_replace('/&apos;/', '&#39;', $text);

        $sectionHtmlPattern = '%            # Rev:20100913_0900 github.com/jmrware/LinkifyURL
                                            # Section text into HTML <A> tags  and everything else.
             (                              # $1: Everything not HTML <A> tag.
               [^<]+(?:(?!<a\b)<[^<]*)*     # non A tag stuff starting with non-"<".
               | (?:(?!<a\b)<[^<]*)+        # non A tag stuff starting with "<".
             )                              # End $1.
             | (                            # $2: HTML <A...>...</A> tag.
                 <a\b[^>]*>                 # <A...> opening tag.
                 [^<]*(?:(?!</a\b)<[^<]*)*  # A tag contents.
                 </a\s*>                    # </A> closing tag.
             )                              # End $2:
             %ix';

        return (string)preg_replace_callback(
            $sectionHtmlPattern,
            /**
             * @param   array  $matches
             *
             * @return string
             */
            static function (array $matches): string {
                return self::linkifyCallback($matches);
            },
            $text
        );
    }

    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param   array  $matches  Matches from the preg_ function
     *
     * @return string
     */
    protected static function linkifyCallback(array $matches): string
    {
        return $matches[2] ?? self::linkifyRegex($matches[1]);
    }


    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param   string  $text  Matches from the preg_ function
     *
     * @return string
     */
    protected static function linkifyRegex(string $text): string
    {
        $urlPattern = '/                                            # Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
                                                                    # Match http & ftp URL that is not already linkified
                                                                    # Alternative 1: URL delimited by (parentheses).
            (\()                                                    # $1 "(" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $2: URL.
            (\))                                                    # $3: ")" end delimiter.
            |                                                       # Alternative 2: URL delimited by [square brackets].
            (\[)                                                    # $4: "[" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $5: URL.
            (\])                                                    # $6: "]" end delimiter.
            |                                                       # Alternative 3: URL delimited by {curly braces}.
            (\{)                                                    # $7: "{" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $8: URL.
            (\})                                                    # $9: "}" end delimiter.
            |                                                       # Alternative 4: URL delimited by <angle brackets>.
            (<|&(?:lt|\#60|\#x3c);)                                 # $10: "<" start delimiter (or HTML entity).
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $11: URL.
            (>|&(?:gt|\#62|\#x3e);)                                 # $12: ">" end delimiter (or HTML entity).
            |                                                       # Alt. 5: URL not delimited by (), [], {} or <>.
            (                                                       # $13: Prefix proving URL not already linked.
            (?: ^                                                   # Can be a beginning of line or string, or
             | [^=\s\'"\]]                                          # a non-"=", non-quote, non-"]", followed by
            ) \s*[\'"]?                                             # optional whitespace and optional quote;
              | [^=\s]\s+                                           # or... a non-equals sign followed by whitespace.
            )                                                       # End $13. Non-prelinkified-proof prefix.
            (\b                                                     # $14: Other non-delimited URL.
            (?:ht|f)tps?:\/\/                                       # Required literal http, https, ftp or ftps prefix.
            [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+                     # All URI chars except "&" (normal*).
            (?:                                                     # Either on a "&" or at the end of URI.
            (?!                                                     # Allow a "&" char only if not start of an...
            &(?:gt|\#0*62|\#x0*3e);                                 # HTML ">" entity, or
            | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]);              # a [&\'"] entity if
            [.!&\',:?;]?                                            # followed by optional punctuation then
            (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$)              # a non-URI char or EOS.
           ) &                                                      # If neg-assertion true, match "&" (special).
            [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]*                     # More non-& URI chars (normal*).
           )*                                                       # Unroll-the-loop (special normal*)*.
            [a-z0-9\-_~$()*+=\/#[\]@%]                              # Last char can\'t be [.!&\',;:?]
           )                                                        # End $14. Other non-delimited URL.
            /imx';

        $urlReplace = '$1$4$7$10$13<a href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';

        return (string)preg_replace($urlPattern, $urlReplace, $text);
    }
}
