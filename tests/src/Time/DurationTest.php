<?php

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
            [123.56, '00:02:03.56'],
            [123.06, '00:02:03.06'],
            [94123.061, '26:08:43.06'],
        ];
    }
}
