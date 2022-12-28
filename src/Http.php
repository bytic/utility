<?php

namespace Nip\Utility;

class Http
{
    protected $server = null;

    public static function getCurrentUrl(): string
    {
        return self::getServerScheme()
            . '://' . self::getServerHost()
            . self::getServerPort()
            . $_SERVER['REQUEST_URI'];
    }

    public static function getServerScheme(): string
    {
        return self::getServerHTTPS() ? 'https' : 'http';
    }

    /**
     * Retrieve HTTPS status from $_SERVER environment variables.
     *
     * @return bool True if the request was performed through HTTPS, false otherwise.
     *
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     */
    public static function getServerHTTPS()
    {
        if (!array_key_exists('HTTPS', $_SERVER)) {
            // not an https-request
            return false;
        }

        if ($_SERVER['HTTPS'] === 'off') {
            // IIS with HTTPS off
            return false;
        }

        // otherwise, HTTPS will be non-empty
        return !empty($_SERVER['HTTPS']);
    }

    /**
     * Retrieve Host value from $_SERVER environment variables.
     *
     * @return string The current host name, including the port if needed. It will use localhost when unable to
     *     determine the current host.
     *
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     */
    private static function getServerHost()
    {
        if (array_key_exists('HTTP_HOST', $_SERVER)) {
            $current = $_SERVER['HTTP_HOST'];
        } elseif (array_key_exists('SERVER_NAME', $_SERVER)) {
            $current = $_SERVER['SERVER_NAME'];
        } else {
            // almost certainly not what you want, but...
            $current = 'localhost';
        }

        if (strstr($current, ":")) {
            $decomposed = explode(":", $current);
            $port       = array_pop($decomposed);
            if (!is_numeric($port)) {
                array_push($decomposed, $port);
            }
            $current = implode(":", $decomposed);
        }

        return $current;
    }

    /**
     * Retrieve the port number from $_SERVER environment variables.
     *
     * @return string The port number prepended by a colon, if it is different than the default port for the protocol
     *     (80 for HTTP, 443 for HTTPS), or an empty string otherwise.
     */
    public static function getServerPort()
    {
        $default_port = self::getServerHTTPS() ? '443' : '80';
        $port         = $_SERVER['SERVER_PORT'] ?? $default_port;

        // Take care of edge-case where SERVER_PORT is an integer
        $port = strval($port);

        if ($port !== $default_port) {
            return ':' . $port;
        }

        return '';
    }
}