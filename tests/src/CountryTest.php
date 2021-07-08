<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Country;

/**
 * Class CountryTest
 * @package Nip\Utility\Tests
 */
class CountryTest extends AbstractTest
{
    public function test_fromName()
    {
        $country = Country::fromName('Romania');

        self::assertInstanceOf(Country::class, $country);
        self::assertSame('Romania', $country->name);
        self::assertSame('ROU', $country->alpha3);
    }

    public function test_fromName_dnx()
    {
        $country = Country::fromName('Roma');

        self::assertInstanceOf(Country::class, $country);
        self::assertNull($country->name);
        self::assertNull($country->alpha3);
    }
}