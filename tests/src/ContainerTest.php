<?php

namespace Nip\Utility\Tests;

use Nip\Container\Container;

use Nip\Utility\Tests\Fixtures\BaseClass;

use function PHPUnit\Framework\assertInstanceOf;

/**
 * Class ContainerTest
 * @package Nip\Utility\Tests
 */
class ContainerTest extends AbstractTest
{
    public function test_container()
    {
        static::assertInstanceOf(Container::class, \Nip\Utility\Container::container());
    }

    public function test_container_with_reset()
    {
        $container = \Nip\Utility\Container::container();
        static::assertInstanceOf(Container::class, $container);
        self::assertSame($container, \Nip\Utility\Container::container());

        Container::setInstance(new Container());
        $container2 = \Nip\Utility\Container::container(true);
        static::assertInstanceOf(Container::class, $container2);
        self::assertNotSame($container, $container2);
    }

    public function test_get()
    {
        $object = \Nip\Utility\Container::get(BaseClass::class);
        self::assertInstanceOf(BaseClass::class, $object);
    }
}
