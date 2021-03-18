<?php

namespace Nip\Utility\Tests\Traits;

use Nip\Utility\Tests\AbstractTest;
use Nip\Utility\Tests\Fixtures\BaseClass;

/**
 * Class CanBootTraitsTraitTest
 * @package Nip\Utility\Tests\Traits
 */
class CanBootTraitsTraitTest extends AbstractTest
{

    public function test_getBootTraits()
    {
        $books = new BaseClass();
        $bootTraits = $books->getBootTraits();
        self::assertEquals(
            ['bootBootableTrait'],
            $bootTraits
        );
    }
}