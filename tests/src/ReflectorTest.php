<?php

declare(strict_types=1);

namespace Nip\Utility\Tests;


use Nip\Utility\Reflector;
use Nip\Utility\Tests\Fixtures\BaseClass;
use Nip\Utility\Tests\Fixtures\JsonModel;
use ReflectionClass;

/**
 *
 */
class ReflectorTest extends AbstractTest
{
    public function test_getParameterClassName()
    {
        $method = (new ReflectionClass(BaseClass::class))->getMethod('methodNameAttribute');

        static::assertSame(JsonModel::class, Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function test_emptyClassName()
    {
        $method = (new ReflectionClass(BaseClass::class))->getMethod('methodNotTypeAttribute');

        static::assertNull(Reflector::getParameterClassName($method->getParameters()[0]));
    }
}
