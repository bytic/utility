<?php

declare(strict_types=1);

namespace Nip\Utility;

use League\ISO3166\Exception\OutOfBoundsException;
use League\ISO3166\ISO3166;

/**
 * Class Country
 * @package Nip\Utility
 */
class Country implements \Stringable
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $alpha2 = null;

    /**
     * @var string
     */
    public $alpha3 = null;

    /**
     * @var string
     */
    public $numeric = null;

    /**
     * @var array
     */
    public $currency = null;

    protected $stringable = 'name';

    /**
     * Country constructor.
     */
    protected function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param $name
     */
    public static function fromName($name)
    {
        if (empty($name)) {
            return new self([]);
        }
        $name = (string)$name;
        if (strlen($name) === 2) {
            return self::fromNameCreate($name, 'alpha2');
        }

        if (strlen($name) === 3) {
            return self::fromNameCreate($name, 'alpha3');
        }

        return self::fromNameCreate($name);
    }

    protected static function fromNameCreate($name, $function = 'name'): Country
    {
        try {
            $data = (new ISO3166())->$function($name);
        } catch (OutOfBoundsException $exception) {
            $data = [];
        }

        return new self($data);
    }

    /**
     * @param $data
     *
     * @return self
     */
    public static function fromData($data): self
    {
        return new self($data);
    }

    public function stringableName()
    {
        $this->stringable = 'name';
    }

    public function stringableAlpha2()
    {
        $this->stringable = 'alpha2';
    }

    public function stringableAlpha3()
    {
        $this->stringable = 'alpha3';
    }

    /**
     * @param $stringable
     *
     * @return void
     */
    public function stringable($stringable): void
    {
        $this->stringable = $stringable;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->{$this->stringable} ?? '';
    }
}