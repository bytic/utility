<?php

declare(strict_types=1);

namespace Nip\Utility\Date;

use DateTimeZone;
use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * @inspiration https://github.com/hill-valley/fluxcap/blob/1.x/src/TimeZone.php
 */
class TimeZone implements JsonSerializable, Stringable
{
    private DateTimeZone $timeZone;


    private function __construct(DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;
    }


    /** @psalm-mutation-free */
    public static function default(): self
    {
        /**
         * @var self|null $timeZone
         * @psalm-suppress ImpureStaticVariable
         */
        static $timeZone;

        /** @psalm-suppress ImpureFunctionCall */
        $name = date_default_timezone_get();

        if (null === $timeZone || $name !== $timeZone->getName()) {
            $timeZone = self::fromString($name);
        }

        return $timeZone;
    }

    public function getName(): string
    {
        return $this->timeZone->getName();
    }

    /** @psalm-pure */
    public static function fromString(string $timeZone): self
    {
        if ('' === $timeZone) {
            throw new InvalidArgumentException('The time zone string can not be empty.');
        }

        try {
            $native = new DateTimeZone($timeZone);
        } catch (Exception) {
            throw new InvalidArgumentException("Unknown time zone \"$timeZone\".");
        }

        return new self($native);
    }

    /** @psalm-pure */
    public static function utc(): self
    {
        /**
         * @var self|null $timeZone
         * @psalm-suppress ImpureStaticVariable
         */
        static $timeZone;

        if (null === $timeZone) {
            $timeZone = self::fromString('UTC'); // @codeCoverageIgnore
        }

        return $timeZone;
    }

    /** @psalm-pure */
    public static function fromNative(DateTimeZone $timeZone): self
    {
        return new self($timeZone);
    }

    /**
     * @param   array{timeZone: DateTimeZone}  $data
     */
    public static function __set_state(array $data): self
    {
        return new self($data['timeZone']);
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function isUtc(): bool
    {
        return 'UTC' === $this->timeZone->getName();
    }

    public function toNative(): DateTimeZone
    {
        return $this->timeZone;
    }

    public function jsonSerialize(): string
    {
        return $this->getName();
    }

    public function __serialize(): array
    {
        return ['name' => $this->getName()];
    }

    /**
     * @param   array{name: string}  $data
     */
    public function __unserialize(array $data): void
    {
        $this->timeZone = new DateTimeZone($data['name']);
    }

    public function __debugInfo(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }
}

