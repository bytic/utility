<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Colors;

/**
 * Class ColorsTest
 * @package Nip\Utility\Tests
 */
class ColorsTest extends AbstractTest
{
    public function test_colors()
    {
        $colors = Colors::colors();
        self::assertIsArray($colors);
        self::assertCount(1567, $colors);
    }


    /**
     * @param $color
     * @param $output
     * @dataProvider data_rgb
     */
    public function test_rgb($color, $output)
    {
        self::assertSame($output, Colors::rgb($color));
    }

    public function data_rgb(): array
    {
        return [
            ['fff', [255, 0, 0]],
            ['0D0E0F', [208, 224, 15]],
        ];
    }

    /**
     * @param $color
     * @param $output
     * @dataProvider data_name
     */
    public function test_name($color, $output)
    {
        $return = Colors::name($color);
        self::assertSame($output, $return[1]);
    }

    public function data_name(): array
    {
        return [
            ['fff', 'White'],
            ['00F', 'Blue'],
        ];
    }
}