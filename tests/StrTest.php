<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Str;

/**
 * Class StrTest
 * @package Nip\Utility\Tests
 */
class StrTest extends AbstractTest
{
    /**
     * @param $data
     * @param $start
     * @param $end
     * @param $masked
     * @dataProvider dataMask()
     */
    public function testMask($data, $start, $end, $masked)
    {
        self::assertSame($masked, Str::mask($data, $start, $end));
    }

    /**
     * @return array
     */
    public function dataMask()
    {
        return [
            ['test', 0, null, '****'],
            ['test', 1, null, 't***'],
            ['foe', 2, 1, '***'],
            ['lorem', 5, false, '*****'],
        ];
    }

    /**
     * @param $name
     * @param $initials
     * @dataProvider dataInitials()
     */
    public function testInitials($name, $initials)
    {
        self::assertSame($initials, Str::initials($name));
    }

    /**
     * @return array
     */
    public function dataInitials()
    {
        return [
            ['test', 'T.'],
            ['test test', 'T.T.'],
            ['test-test', 'T.T.'],
        ];
    }
}
