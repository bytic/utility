<?php

declare(strict_types=1);

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

    public function test_fromNamePartial()
    {
        $country = Country::fromName('Romani');

        self::assertInstanceOf(Country::class, $country);
        self::assertSame('Romania', $country->name);
        self::assertSame('ROU', $country->alpha3);
    }

    public function test_fromName_dnx()
    {
        $country = Country::fromName('Rona');

        self::assertInstanceOf(Country::class, $country);
        self::assertNull($country->name);
        self::assertNull($country->alpha3);
    }


    public function test_stringable()
    {
        $country = Country::fromName('Romania');
        self::assertSame('Romania', (string)$country);
    }

    public function test_stringable_alpha3()
    {
        $country = Country::fromName('Romania');
        $country->stringableAlpha3();
        self::assertSame('ROU', (string)$country);
    }
}