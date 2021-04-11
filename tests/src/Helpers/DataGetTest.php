<?php

namespace Nip\Utility\Tests\Helpers;

use Nip\Utility\Tests\AbstractTest;

/**
 * Class DataGetTest
 * @package Nip\Utility\Tests\Helpers
 */
class DataGetTest extends AbstractTest
{
    public function test_data_get_with_closure()
    {
        $item = new \stdClass();
        $item->test = 1;

        self::assertSame(
            1,
            data_get(
                $item,
                function ($item) {
                    return $item->test;
                }
            )
        );
    }
}
