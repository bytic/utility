<?php

namespace Nip\Utility;

use League\ISO3166\ISO3166;

/**
 * Class Country
 * @package Nip\Utility
 */
class Country
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
        try {
            $data = (new ISO3166())->name($name);
        } catch (\League\ISO3166\Exception\OutOfBoundsException $exception) {
            $data = [];
        }

        return new self($data);
    }

}