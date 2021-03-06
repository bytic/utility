<?php

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

    public function toArray(): array
    {
        return [];
    }
}
