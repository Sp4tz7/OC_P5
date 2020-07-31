<?php

namespace Core;

/**
 * Trait Hydrator
 * @package Core
 */
trait Hydrator
{
    /**
     * @param $data
     */
    public function hydrate($data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (is_callable([$this, $method])) {
                $this->$method($value);
            }
        }
    }
}
