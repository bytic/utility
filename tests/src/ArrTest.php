<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Arr;

/**
 * Class ArrTest
 * @package Nip\Utility\Tests
 */
class ArrTest extends AbstractTest
{
    public function testPluck()
    {
        $data = [
            'post-1' => [
                'comments' => [
                    'tags' => ['#foo', '#bar',],
                ],
            ],
            'post-2' => [
                'comments' => [
                    'tags' => ['#baz',],
                ],
            ],
        ];
        static::assertEquals(
            [
                0 => [
                    'tags' => ['#foo', '#bar',],
                ],
                1 => [
                    'tags' => ['#baz',],
                ],
            ],
            Arr::pluck($data, 'comments')
        );


        static::assertEquals([['#foo', '#bar'], ['#baz']], Arr::pluck($data, 'comments.tags'));
        static::assertEquals([null, null], Arr::pluck($data, 'foo'));
        static::assertEquals([null, null], Arr::pluck($data, 'foo.bar'));

        $array = [
            ['developer' => ['name' => 'Taylor']],
            ['developer' => ['name' => 'Abigail']],
        ];
        $array = Arr::pluck($array, 'developer.name');
        static::assertEquals(['Taylor', 'Abigail'], $array);
    }

    public function test_forgetValues()
    {
        $array = ['11', '22', '33'];
        self::assertSame(['11'], Arr::forgetValues($array, '22', '33'));
    }

    public function testInsertBeforeKey()
    {
        // Test numeric array insertion
        $array  = [1, 3];
        $result = Arr::insertBeforeKey($array, 1, [2]);
        self::assertSame(
            [1, 2, 3],
            $result
        );

        // Test associative array insertion
        $array  = ['first' => 1, 'third' => 3];
        $result = Arr::insertBeforeKey($array, 'third', ['second' => 2]);
        self::assertSame(
            ['first' => 1, 'second' => 2, 'third' => 3],
            $result
        );

        // Test basic array insertion
        $array  = ['first' => 1, 'third' => 3];
        $result = Arr::insertBeforeKey($array, 'third', ['second' => 2]);
        self::assertSame(
            ['first' => 1, 'second' => 2, 'third' => 3],
            $result
        );

        // Test inserting before non-existing key
        $array  = ['a' => 1, 'b' => 2];
        $result = Arr::insertBeforeKey($array, 'non-existing', ['c' => 3]);
        self::assertSame(
            ['a' => 1, 'b' => 2, 'c' => 3],
            $result
        );

        // Test with multi-dimensional array
        $array  = [
            'first' => ['id' => 1],
            'third' => ['id' => 3]
        ];
        $result = Arr::insertBeforeKey($array, 'third', ['second' => ['id' => 2]]);
        self::assertSame(
            [
                'first'  => ['id' => 1],
                'second' => ['id' => 2],
                'third'  => ['id' => 3]
            ],
            $result
        );
    }


}
