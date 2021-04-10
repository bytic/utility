<?php

namespace Nip\Utility\Tests\Fixtures;

/**
 * Class BaseClass
 * @package Nip\Utility\Tests\Fixtures
 */
class JsonModel extends BaseClass implements \JsonSerializable
{
    public $data = ['json' => 'serializable'];

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
