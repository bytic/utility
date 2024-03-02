<?php

namespace Nip\Utility\Tests\Helpers;

use Exception;
use Nip\Utility\Tests\AbstractTest;

/**
 * Class DataGetTest
 * @package Nip\Utility\Tests\Helpers
 */
class DatetimeTest extends AbstractTest
{
    /**
     * @param                $result
     * @param   string       $format
     * @param                $timestamp
     * @param   string|null  $locale
     *
     * @return void
     * @throws Exception
     * @dataProvider data_bytic_strftime
     */
    public function test_bytic_strftime($result, string $format, $timestamp = null, ?string $locale = null)
    {
        self::assertSame(
            $result,
            bytic_strftime(
                $format,
                $timestamp,
                $locale
            )
        );
    }

    public function data_bytic_strftime()
    {
        return [
            ['01/05/2023', '%d/%m/%Y', '2023-05-01']
        ];
    }
}
