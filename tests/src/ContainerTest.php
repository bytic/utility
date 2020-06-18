<?php

namespace Nip\Utility\Tests;

use Nip\Container\Container;

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
}
