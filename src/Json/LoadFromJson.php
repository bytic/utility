<?php

declare(strict_types=1);

namespace Nip\Utility\Json;

use function json_decode;

/**
 *
 */
trait LoadFromJson
{

    public static function fromJsonString(string $string): self
    {
        $data = json_decode($string, true, 512, JSON_THROW_ON_ERROR);

        $object = new static();
        $object->__unserialize($data);

        return $object;
    }

    /**
     * @param $param
     *
     * @return mixed
     */
    abstract public function __unserialize($param);
}

