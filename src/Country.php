<?php

declare(strict_types=1);

namespace Nip\Utility;

use League\ISO3166\Exception\OutOfBoundsException;
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
        } catch (OutOfBoundsException $exception) {
            $data = [];
        }

        return new self($data);
    }

}