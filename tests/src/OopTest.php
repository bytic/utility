<?php

declare(strict_types=1);

namespace Nip\Utility\Tests;

use Nip\Utility\Oop;
use Nip\Utility\Tests\Fixtures\BaseClass;
use Nip\Utility\Tests\Fixtures\ExtendedClass;
use Nip\Utility\Traits\HasRequestTrait;
use Nip\Utility\Traits\NameWorksTrait;
use SplFileInfo;

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
        self::assertTrue(Oop::classUsesTrait(ExtendedClass::class, NameWorksTrait::class));
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

    public function test_classesInFile()
    {
        $info = new SplFileInfo(TEST_FIXTURE_PATH . '/BaseClass.php');
        self::assertSame([BaseClass::class], Oop::classesInFile($info));
    }

    public function test_propertyIsInitialized()
    {
        $object = new BaseClass();
        self::assertFalse(Oop::propertyIsInitialized($object, 'property'));
        $object->property = 'value';

        self::assertTrue(Oop::propertyIsInitialized($object, 'property'));
    }
}
