<?php

namespace Nip\Utility\Traits;

use Nip\Utility\Oop;

/**
 * Trait CanBootTraitsTrait
 * @package Nip\Utility\Traits
 */
trait CanBootTraitsTrait
{
    protected $bootTraits = null;
    protected $bootedTraits = [];

    /**
     * Initialize any initializable traits on the model.
     *
     * @return void
     */
    public function bootTraits()
    {
        $needBooting = array_diff($this->getBootTraits(), $this->bootedTraits);
        foreach ($needBooting as $method) {
            $this->{$method}();
            $this->bootedTraits[] = $method;
        }
    }

    /**
     * @return null
     */
    public function getBootTraits()
    {
        if ($this->bootTraits === null) {
            $this->initBootTraits();
        }
        return $this->bootTraits;
    }

    /**
     * @param null $bootTraits
     */
    public function setBootTraits($bootTraits): void
    {
        $this->bootTraits = $bootTraits;
    }

    /**
     * @return bool
     */
    public function hasBootTraits()
    {
        return is_array($this->getBootTraits());
    }

    protected function initBootTraits()
    {
        $this->setBootTraits($this->generateBootTraits());
    }

    /**
     * @return array
     */
    protected function generateBootTraits()
    {
        $traitBoots = [];
        $class = static::class;

        foreach (Oop::uses($class) as $trait) {
            $method = 'boot' . Oop::basename($trait);

            if (method_exists($class, $method)) {
                $traitBoots[] = $method;
            }
        }
        return $traitBoots;
    }
}
