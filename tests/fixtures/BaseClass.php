<?php

declare(strict_types=1);

namespace Nip\Utility\Tests\Fixtures;

use Nip\Utility\Traits\CanBootTraitsTrait;
use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class BaseClass
 * @package Nip\Utility\Tests\Fixtures
 */
class BaseClass
{
    use NameWorksTrait;
    use CanBootTraitsTrait;
    use Traits\BootableTrait;

    public mixed $property;

    public function toArray(): array
    {
        return [];
    }

    /**
     * @param $attribute
     *
     * @return mixed
     */
    public function methodNotTypeAttribute($attribute)
    {
        return $attribute;
    }

    /**
     * @param   JsonModel  $model
     *
     * @return mixed
     */
    public function methodNameAttribute(JsonModel $model)
    {
        return get_class($model);
    }
}
