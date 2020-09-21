<?php

namespace Nip\Utility\Tests\Fixtures;

use Nip\Utility\Tests\Fixtures\Traits\ExtendedTrait;
use Nip\Utility\Traits\SingletonTrait;

/**
 * Class ExtendedClass
 * @package Nip\Utility\Tests\Fixtures
 */
class ExtendedClass extends BaseClass
{
    use SingletonTrait;
    use ExtendedTrait;
}
