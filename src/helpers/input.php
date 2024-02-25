<?php

if (!function_exists('fix_input_quotes')) {
    /**
     * @deprecated use of get_magic_quotes_gpc is deprecated
     */
    function fix_input_quotes()
    {
//        if (get_magic_quotes_gpc()) {
//            array_stripslashes($_GET);
//            array_stripslashes($_POST);
//            array_stripslashes($_COOKIE);
//        }
    }
}

if (!function_exists('array_stripslashes')) {
    function array_stripslashes(&$array)
    {
        if (!is_array($array)) {
            return;
        }
        foreach ($array as $k => $v) {
            if (is_array($array[$k])) {
                array_stripslashes($array[$k]);
            } else {
                $array[$k] = stripslashes($array[$k]);
            }
        }
        return $array;
    }
}

if (!function_exists('clean')) {
    function clean($input)
    {
        return trim(stripslashes(htmlentities($input, ENT_QUOTES, 'UTF-8')));
    }
}

if (!function_exists('strToASCII')) {
    function strToASCII($str)
    {
        $trans = [
            'Š' => 'S',
            'Ș' => 'S',
            'š' => 's',
            'ș' => 's',
            'Ð' => 'Dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Ă' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Ț' => 'T',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'ă' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'ƒ' => 'f',
            'ț' => 't'
        ];
        return strtr($str, $trans);
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (!function_exists('json_decode')) {
    /**
     * @param         $json
     * @param   bool  $assoc
     * @param   int   $n
     * @param   int   $state
     * @param   int   $waitfor
     *
     * @return array|float|int|mixed|null|stdClass|string
     */
    function json_decode(
        $json,
        $assoc = false, /* emu_args */
        $n = 0,
        $state = 0,
        $waitfor = 0
    ) {
        //-- result var
        $val = null;
        static $lang_eq = ["true" => true, "false" => false, "null" => null];
        static $str_eq = [
            "n"  => "\012",
            "r"  => "\015",
            "\\" => "\\",
            '"'  => '"',
            'f'  => "\f",
            'b'  => "\b",
            't'  => "\t",
            '/'  => '/',
        ];

        //-- flat char-wise parsing
        for (/* n */; $n < strlen($json); /* n */) {
            $c = $json[$n];

            //-= in-string
            if ($state === '"') {
                if ($c == '\\') {
                    $c = $json[++$n];
                    // simple C escapes
                    if (isset($str_eq[$c])) {
                        $val .= $str_eq[$c];
                    } // here we transform \uXXXX Unicode (always 4 nibbles) references to UTF-8
                    elseif ($c == 'u') {
                        // read just 16bit (therefore value can't be negative)
                        $hex = hexdec(substr($json, $n + 1, 4));
                        $n   += 4;
                        // Unicode ranges
                        if ($hex < 0x80) { // plain ASCII character
                            $val .= chr($hex);
                        } elseif ($hex < 0x800) {   // 110xxxxx 10xxxxxx
                            $val .= chr(0xC0 + $hex >> 6) . chr(0x80 + $hex & 63);
                        } elseif ($hex <= 0xFFFF) { // 1110xxxx 10xxxxxx 10xxxxxx
                            $val .= chr(0xE0 + $hex >> 12) . chr(0x80 + ($hex >> 6) & 63) . chr(0x80 + $hex & 63);
                        }
                        // other ranges, like 0x1FFFFF=0xF0, 0x3FFFFFF=0xF8 and 0x7FFFFFFF=0xFC do not apply
                    }

                    // no escape, just a redundant backslash
                    //@COMPAT: we could throw an exception here
                    else {
                        $val .= '\\' . $c;
                    }
                } // end of string
                elseif ($c == '"') {
                    $state = 0;
                } // yeeha! a single character found!!!!1!
                else /* if (ord($c) >= 32) */ { //@COMPAT: specialchars check - but native json doesn't do it?
                    $val .= $c;
                }
            } //-> end of sub-call (array/object)
            elseif ($waitfor && (strpos($waitfor, $c) !== false)) {
                return [$val, $n]; // return current value and state
            } //-= in-array
            elseif ($state === ']') {
                list($v, $n) = json_decode($json, 0, $n, 0, ',]');
                $val[] = $v;
                if ($json[$n] == ']') {
                    return [$val, $n];
                }
            } //-= in-object
            elseif ($state === '}') {
                list($i, $n) = json_decode($json, 0, $n, 0, ':'); // this allowed non-string indicies
                list($v, $n) = json_decode($json, 0, $n + 1, 0, ',}');
                $val[$i] = $v;
                if ($json[$n] == '}') {
                    return [$val, $n];
                }
            } //-- looking for next item (0)
            else {
                //-> whitespace
                if (preg_match("/\s/", $c)) {
                    // skip
                } //-> string begin
                elseif ($c == '"') {
                    $state = '"';
                } //-> object
                elseif ($c == '{') {
                    list($val, $n) = json_decode($json, $assoc, $n + 1, '}', '}');
                    if ($val && $n && !$assoc) {
                        $obj = new stdClass();
                        foreach ($val as $i => $v) {
                            $obj->{$i} = $v;
                        }
                        $val = $obj;
                        unset($obj);
                    }
                } //-> array
                elseif ($c == '[') {
                    list($val, $n) = json_decode($json, $assoc, $n + 1, ']', ']');
                } //-> comment
                elseif (($c == '/') && ($json[$n + 1] == '*')) {
                    // just find end, skip over
                    ($n = strpos($json, '*/', $n + 1)) or ($n = strlen($json));
                } //-> numbers
                elseif (preg_match("#^(-?\d+(?:\.\d+)?)(?:[eE]([-+]?\d+))?#", substr($json, $n), $uu)) {
                    $val = $uu[1];
                    $n   += strlen($uu[0]) - 1;
                    if (strpos($val, '.')) {  // float
                        $val = (float)$val;
                    } elseif ($val[0] == '0') {  // oct
                        $val = octdec($val);
                    } else {
                        $val = (int)$val;
                    }
                    // exponent?
                    if (isset($uu[2])) {
                        $val *= pow(10, (int)$uu[2]);
                    }
                } //-> boolean or null
                elseif (preg_match("#^(true|false|null)\b#", substr($json, $n), $uu)) {
                    $val = $lang_eq[$uu[1]];
                    $n   += strlen($uu[1]) - 1;
                } //-- parsing error
                else {
                    // PHPs native json_decode() breaks here usually and QUIETLY
                    trigger_error("json_decode: error parsing '$c' at position $n", E_USER_WARNING);

                    return $waitfor ? [null, 1 << 30] : null;
                }
            }//state
            //-- next char
            if ($n === null) {
                return;
            }
            $n++;
        }//for
        //-- final result
        return $val;
    }
}
