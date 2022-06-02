<?php

declare(strict_types=1);

namespace Nip\Utility\Tests\Time;

use Nip\Utility\Tests\AbstractTest;
use Nip\Utility\Time\Duration;

/**
 * Class DurationTest
 * @package Nip\Utility\Tests\Time
 */
class DurationTest extends AbstractTest
{
    /**
     * @param $seconds
     * @param $formatted
     * @dataProvider dataGetDefaultString
     */
    public function testGetDefaultString($seconds, $formatted)
    {
        $duration = new Duration($seconds);
        self::assertEquals($formatted, $duration->getDefaultString());
    }

    /**
     * @return array
     */
    public function dataGetDefaultString()
    {
        return [
            [123, '00:02:03.00'],
            [123.56, '00:02:03.56'],
            [123.06, '00:02:03.06'],
            [94123.061, '26:08:43.06'],
        ];
    }

    /**
     * @param $input
     * @param $output
     *
     * @dataProvider data_cronoTimeInSeconds
     */
    public function test_parseFromString($input, $output)
    {
        $helper = new Duration($input);

        self::assertEquals($output, $helper->getSeconds());
    }

    public function data_cronoTimeInSeconds(): array
    {
        return [
            ['0:50', 50],
            ['0:50', 50],
            ['1:50', 110],
            ['1:1:50', 3710],
        ];
    }
}
