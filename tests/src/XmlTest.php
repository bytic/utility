<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Xml;

/**
 * Class UuidTest
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
}
