<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class UuidTest
 * @package Nip\Utility\Tests
 */
class UuidTest extends AbstractTest
{
    public function test_uuid()
    {
        $uuid = Uuid::uuid();
        self::assertIsObject($uuid);
        $uuid = (string) $uuid;
        self::assertIsString($uuid);
        self::assertTrue(Uuid::isValid($uuid));
        self::assertSame(36, strlen($uuid));
    }

    /**
     * @dataProvider validUuidList
     * @param $uuid
     */
    public function test_isValid_validList($uuid)
    {
        static::assertTrue(Uuid::isValid($uuid));
    }

    /**
     * @dataProvider invalidUuidList
     * @param $uuid
     */
    public function test_isValid_invalidList($uuid)
    {
        static::assertFalse(Uuid::isValid($uuid));
    }

    /**
     * @dataProvider validUuidList
     * @param $uuid
     */
    public function test_fromString($string)
    {
        $uuid = Uuid::fromString($string);
        static::assertInstanceOf(UuidInterface::class, $uuid);
        self::assertStringContainsStringIgnoringCase($uuid->toString(), $string);
    }

    /**
     * @return \string[][]LazyUuidFromString
     */
    public function validUuidList()
    {
        return [
            ['a0a2a2d2-0b87-4a18-83f2-2529882be2de'],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1'],
            ['00000000-0000-0000-0000-000000000000'],
            ['e60d3f48-95d7-4d8d-aad0-856f29a27da2'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-21e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-31e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-41e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-51e1-9b21-0800200c9a66'],
            ['FF6F8CB0-C57D-11E1-9B21-0800200C9A66'],
        ];
    }

    /**
     * @return \string[][]
     */
    public function invalidUuidList()
    {
        return [
            ['not a valid uuid so we can test this'],
            ['zf6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1' . PHP_EOL],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1 '],
            [' 145a1e72-d11d-11e8-a8d5-f2801f1b9fd1'],
            ['145a1e72-d11d-11e8-a8d5-f2z01f1b9fd1'],
            ['3f6f8cb0-c57d-11e1-9b21-0800200c9a6'],
            ['af6f8cb-c57d-11e1-9b21-0800200c9a66'],
            ['af6f8cb0c57d11e19b210800200c9a66'],
            ['ff6f8cb0-c57da-51e1-9b21-0800200c9a66'],
        ];
    }
}
