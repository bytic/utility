<?php

namespace Nip\Utility\Tests\Traits;

use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class NameWorksTraitTest
 * @package Nip\Tests\Utility\Traits
 */
class NameWorksTraitTest extends \Nip\Utility\Tests\AbstractTest
{
    use NameWorksTrait;

    public function testGetClassName()
    {
        self::assertSame('Nip\Utility\Tests\Traits\NameWorksTraitTest', $this->getClassName());

        $name = 'Userrs';
        $this->setClassName($name);
        self::assertSame($name, $this->getClassName());
    }

    public function testIsNamespaced()
    {
        self::assertTrue($this->isNamespaced());
    }

    public function testGetNamespaceParentFolder()
    {
        self::assertSame('Traits', $this->getNamespaceParentFolder());
        self::assertSame(
            ['Nip', 'Utility','Tests',  'Traits', 'NameWorksTraitTest'],
            $this->getClassNameParts()
        );
    }
}
