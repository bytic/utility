<?php


declare(strict_types=1);

namespace Nip\Utility\Countries;

use League\ISO3166\ISO3166;
use Nip\Utility\Country;
use Nip\Utility\Traits\SingletonTrait;
use OutOfBoundsException;

/**
 *
 */
class Countries
{
    use SingletonTrait;

    /**
     * @var array
     */
    protected $countries = [];

    protected $iso3166 = null;

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $this->iso3166 = new ISO3166();
        foreach ($this->iso3166->all() as $country) {
            $this->countries[$country['name']] = Country::fromData($country);
        }
    }

    public static function all(): array
    {
        return self::instance()->countries;
    }

    /**
     * @param $name
     *
     * @return Country
     */
    public static function get($name)
    {
        return static::instance()->lookup(ISO3166::KEY_NAME, $name);
    }

    private function lookup(string $key, string $value): Country
    {
        $value = mb_strtolower($value);

        foreach ($this->countries as $country) {
            $comparison = mb_strtolower($country->{$key});

            if ($value === $comparison || $value === mb_substr($comparison, 0, mb_strlen($value))) {
                return $country;
            }
        }

        throw new OutOfBoundsException(sprintf('No "%s" key found matching: %s', $key, $value));
    }
}

