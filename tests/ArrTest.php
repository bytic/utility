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
}
