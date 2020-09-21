<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Oop;
use Nip\Utility\Tests\Fixtures\ExtendedClass;

/**
 * Class OopTest
 * @package Nip\Utility\Tests
 */
class OopTest extends AbstractTest
{
    public function test_basename()
    {
        $name = Oop::basename(ExtendedClass::class);
        self::assertSame('ExtendedClass', $name);
    }

    public function test_uses()
    {
        $traits = Oop::uses(ExtendedClass::class);
        self::assertSame(
            [
                'Nip\Utility\Traits\NameWorksTrait' => 'Nip\Utility\Traits\NameWorksTrait',
                'Nip\Utility\Traits\SingletonTrait' => 'Nip\Utility\Traits\SingletonTrait',
                'Nip\Utility\Tests\Fixtures\Traits\ExtendedTrait' => 'Nip\Utility\Tests\Fixtures\Traits\ExtendedTrait',
                'Nip\Utility\Traits\DynamicPropertiesTrait' => 'Nip\Utility\Traits\DynamicPropertiesTrait'
            ],
            $traits
        );
    }
}
