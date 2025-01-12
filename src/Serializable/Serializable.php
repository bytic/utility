<?php

declare(strict_types=1);

namespace Nip\Utility\Serializable;

use Nip\Utility\Json\LoadFromJson;

/**
 *
 */
trait Serializable
{
    use LoadFromJson;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__serialize();
    }

    abstract public function __serialize(): array;

    public function serialize(): ?string
    {
        return serialize($this->__serialize());
    }

    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data));
    }
}

