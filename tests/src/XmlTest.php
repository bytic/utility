<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Xml;

/**
 * Class XmlTest
 * @package Nip\Utility\Tests
 */
class XmlTest extends AbstractTest
{
    public function test_validate_with_url()
    {
        $this->expectNotToPerformAssertions();

        Xml::validate(
            file_get_contents(TEST_FIXTURE_PATH . '/Xml/request.xml'),
            'https://secure.plationline.ro/xml_validation/po.request.v5.xsd'
        );
    }

    public function test_toObject()
    {
        $xml = file_get_contents(TEST_FIXTURE_PATH . '/Xml/request.xml');
        $object = Xml::toObject($xml);
        self::assertInstanceOf(\SimpleXMLElement::class, $object);
    }

    public function test_fromArray()
    {
        $object = Xml::fromArray(['myKey' => ['myAtrr' => 5, 'myAttr2' => 'My Value']]);
        self::assertInstanceOf(\SimpleXMLElement::class, $object);
        self::assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>
<myKey><myAtrr>5</myAtrr><myAttr2>My Value</myAttr2></myKey>
',
            $object->asXML()
        );
    }
}
