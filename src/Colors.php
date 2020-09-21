<?php

namespace Nip\Utility;

/**
 * Class Colors
 * @package Nip\Utility
 */
class Colors
{
    /**
     * @return mixed
     */
    public static function colors()
    {
        static $colors = null;
        if ($colors === null) {
            $colors = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'colors.php';
        }
        return $colors;
    }

    /**
     * @param $color
     * @return array
     */
    public static function rgb($color)
    {
        $red = substr($color, 1, 2);
        $green = substr($color, 3, 2);
        $blue = substr($color, 5, 2);

        $red = hexdec($red);
        $green = hexdec($green);
        $blue = hexdec($blue);

        return [$red, $green, $blue];
    }

    /**
     * @param string $color
     * @return array
     */
    public static function hsl($color)
    {
        list($red, $green, $blue) = static::rgb($color);

        $red /= 255;
        $green /= 255;
        $blue /= 255;

        $min = min([$red, min([$green, $blue])]);
        $max = max([$red, max([$green, $blue])]);

        $delta = $max - $min;
        $lightness = ($min + $max) / 2;

        $saturation = 0;
        if ($lightness > 0 && $lightness < 1) {
            $saturation = $delta / ($lightness < 0.5 ? (2 * $lightness) : 2 - (2 * $lightness));
        }

        $hue = 0;
        if ($delta > 0) {
            if ($max == $red && $max != $green) {
                $hue += ($green - $blue) / $delta;
            }
            if ($max == $green && $max != $blue) {
                $hue += (2 + ($blue - $red) / $delta);
            }
            if ($max == $blue && $max != $red) {
                $hue += (4 + ($red - $green) / $delta);
            }

            $hue /= 6;
        }

        $hue *= 255;
        $saturation *= 255;
        $lightness *= 255;

        return [floor($hue), floor($saturation), floor($lightness)];
    }


    /**
     * @param $color
     * @return array|bool
     */
    public static function name($color)
    {
        $color = strtoupper($color);

        if (strlen($color) < 3 || strlen($color) > 7) {
            return false;
        }

        if (strlen($color) % 3 == 0) {
            $color = '#' . $color;
        }

        if (strlen($color) == 4) {
            $color = '#' . $color[1] . $color[1] . $color[2] . $color[2] . $color[3] . $color[3];
        }

        list($red, $green, $blue) = static::rgb($color);
        list($h, $s, $l) = static::hsl($color);

        $ndf1 = 0;
        $ndf2 = 0;
        $ndf = 0;
        $cl = -1;
        $df = -1;

        $count = count(static::colors());
        $colors = static::colors();

        for ($index = 0; $index < $count; $index++) {
            if ($color == '#' . $colors[$index][0]) {
                return ['#' . $colors[$index][0], $colors[$index][1], true];
            }

            $ndf1 = pow($red - $colors[$index][2], 2) + pow(
                $green - $colors[$index][3],
                2
            ) + pow($blue - $colors[$index][4], 2);
            $ndf2 = abs(pow($h - $colors[$index][5], 2)) + pow(
                $s - $colors[$index][6],
                2
            ) + abs(pow($l - $colors[$index][7], 2));

            $ndf = $ndf1 + $ndf2 * 2;
            if ($df < 0 || $df > $ndf) {
                $df = $ndf;
                $cl = $index;
            }
        }

        return $cl < 0 ? false : ["#" . static::colors()[$cl][0], static::colors()[$cl][1], false];
    }


    /**
     * Uses luminosity to calculate the difference between the given colors.
     * The returned value should be bigger than 5 for best readability.
     *
     * @param string|array $color1
     * @param string|array $color2
     * @return double
     */
    public static function lumDiff($color1, $color2)
    {
        list($red1, $green1, $blue1) = is_array($color1) ? $color1 : static::rgb($color1);
        list($red2, $green2, $blue2) = is_array($color2) ? $color2 : static::rgb($color2);

        $lightness1 = 0.2126 * pow($red1 / 255, 2.2) +
            0.7152 * pow($green1 / 255, 2.2) +
            0.0722 * pow($blue1 / 255, 2.2);

        $lightness2 = 0.2126 * pow($red2 / 255, 2.2) +
            0.7152 * pow($green2 / 255, 2.2) +
            0.0722 * pow($blue2 / 255, 2.2);

        if ($lightness1 > $lightness2) {
            return ($lightness1 + 0.05) / ($lightness2 + 0.05);
        } else {
            return ($lightness2 + 0.05) / ($lightness1 + 0.05);
        }
    }
}
