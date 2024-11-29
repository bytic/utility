<?php

namespace Nip\Utility\Tests;

use InvalidArgumentException;
use Nip\Utility\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{

    /**
     * @dataProvider data_IntVal
     * @return void
     */
    public function test_intVal($value, $expected, $exception = null)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        self::assertSame($expected, Number::intVal($value));
    }

    public function data_IntVal()
    {
        return [
            [null, null, null],
            [0, 0, null],
            [10, 10, null],
            [10.0, 10, null],
            [10.5, 10, InvalidArgumentException::class],
            ['10', 10, null],
            ['10.5', 10, InvalidArgumentException::class],
            ['10.0', 10, null],
        ];
    }
}
