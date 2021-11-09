<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Stringable;

/**
 * Class StringableTest
 * @package Nip\Utility\Tests
 */
class StringableTest extends AbstractTest
{

    public function testIsAscii()
    {
        $this->assertTrue($this->stringable('A')->isAscii());
        $this->assertFalse($this->stringable('ù')->isAscii());
    }


    public function testAsciiWithSpecificLocale()
    {
        $this->assertSame('h H sht Sht a A ia yo', (string) $this->stringable('х Х щ Щ ъ Ъ иа йо')->ascii('bg'));
        $this->assertSame('ae oe ue Ae Oe Ue', (string) $this->stringable('ä ö ü Ä Ö Ü')->ascii('de'));
    }

    public function testWordCount()
    {
        $this->assertEquals(2, $this->stringable('Hello, world!')->wordCount());
        $this->assertEquals(10, $this->stringable('Hi, this is my first contribution to the Laravel framework.')->wordCount());
    }

    /**
     * @param   string  $string
     *
     * @return Stringable
     */
    protected function stringable($string = '')
    {
        return new Stringable($string);
    }
}