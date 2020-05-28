<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Url;

/**
 * Class StrTest
 * @package Nip\Utility\Tests
 */
class UrlTest extends AbstractTest
{

    /**
     * @dataProvider data_isValid()
     * @param $url
     * @param $output
     */
    public function test_isValid($url, $absolute, $output)
    {
        self::assertSame($output, Url::isValid($url, $absolute));
    }

    /**
     * @return array
     */
    public function data_isValid()
    {
        return [
            ['test', true, false],
            ['google.ro', true, false],
            ['http://google.ro', true, true],
        ];
    }
}
