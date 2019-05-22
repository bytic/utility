<?php

namespace Nip\Utility;

/**
 * Class Email
 * @package Nip\Utility
 */
class Email
{
    /**
     * @param $email
     * @return mixed
     */
    public static function mask($email)
    {
        if (strpos($email, '@') === false) {
            return Str::mask($email, 1);
        }

        list($username, $domain) = explode("@", $email);

        $parts[] = Str::mask($username, 1);
        $parts[] = '@';

        $domainParts = explode('.', $domain);
        $lastPart = array_pop($domainParts);
        foreach ($domainParts as $part) {
            $parts[] = Str::mask($part, 1);
            $parts[] = '.';
        }
        $parts[] = $lastPart;
        return implode('', $parts);
    }
}
