<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Oop;
use Nip\Utility\Tests\Fixtures\ExtendedClass;
use Nip\Utility\Traits\HasRequestTrait;

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

    public function test_classUsesTrait()
    {
        self::assertTrue(Oop::classUsesTrait(ExtendedClass::class, \Nip\Utility\Traits\NameWorksTrait::class));
        self::assertFalse(Oop::classUsesTrait(ExtendedClass::class, HasRequestTrait::class));
    }

    public function test_uses()
    {
        $traits = Oop::uses(ExtendedClass::class);
        self::assertSame(
            [
                'Nip\Utility\Traits\NameWorksTrait'               => 'Nip\Utility\Traits\NameWorksTrait',
                'Nip\Utility\Traits\CanBootTraitsTrait'           => 'Nip\Utility\Traits\CanBootTraitsTrait',
                'Nip\Utility\Tests\Fixtures\Traits\BootableTrait' => 'Nip\Utility\Tests\Fixtures\Traits\BootableTrait',
                'Nip\Utility\Traits\SingletonTrait'               => 'Nip\Utility\Traits\SingletonTrait',
                'Nip\Utility\Tests\Fixtures\Traits\ExtendedTrait' => 'Nip\Utility\Tests\Fixtures\Traits\ExtendedTrait',
                'Nip\Utility\Traits\DynamicPropertiesTrait'       => 'Nip\Utility\Traits\DynamicPropertiesTrait'
            ],
            $traits
        );
    }
}
