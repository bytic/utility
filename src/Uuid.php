<?php

namespace Nip\Utility;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Class Uuid
 * @package Nip\Utility
 */
class Uuid
{

    /**
     * The callback that should be used to generate UUIDs.
     *
     * @var callable
     */
    protected static $uuidFactory;

    /**
     * Generate a UUID (version 4).
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public static function uuid()
    {
        return static::$uuidFactory
            ? call_user_func(static::$uuidFactory)
            : RamseyUuid::uuid4();
    }

    /**
     * Generate a UUID (version 4).
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public static function v4()
    {
        return RamseyUuid::uuid4();
    }

    /**
     * Generate a UUID (version 5).
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public static function v5($ns, string $name)
    {
        return RamseyUuid::uuid5($ns, $name);
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function fromString($uuid)
    {
        return Uuid::fromString($uuid);
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function fromBinary($uuid)
    {
        return Uuid::fromBinary($uuid);
    }

    /**
     * @param $uuid
     * @return bool
     */
    public static function isValid($uuid)
    {
        if (!is_string($uuid)) {
            return false;
        }

        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $uuid) > 0;
    }

    /**
     * Set the callable that will be used to generate UUIDs.
     *
     * @param callable|null $factory
     * @return void
     */
    public static function createUsing(callable $factory = null)
    {
        static::$uuidFactory = $factory;
    }

    /**
     * Indicate that UUIDs should be created normally and not using a custom factory.
     *
     * @return void
     */
    public static function createNormally()
    {
        static::$uuidFactory = null;
    }
}
