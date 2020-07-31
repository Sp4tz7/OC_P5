<?php

namespace Entity;

use Core\Hydrator;

/**
 * Class Entity
 * @package Entity
 */
abstract class Entity implements \ArrayAccess
{
    use Hydrator;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var
     */
    protected $id;

    /**
     * Entity constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return empty($this->id);
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $objectID
     */
    public function setId($objectID)
    {
        $this->id = (int)$objectID;
    }

    /**
     * @param mixed $var
     * @return mixed
     */
    public function offsetGet($var)
    {
        if (isset($this->$var) && is_callable([$this, $var])) {
            return $this->$var();
        }
    }

    /**
     * @param mixed $var
     * @param mixed $value
     */
    public function offsetSet($var, $value)
    {
        $method = 'set'.ucfirst($var);

        if (isset($this->$var) && is_callable([$this, $method])) {
            $this->$method($value);
        }
    }

    /**
     * @param mixed $var
     * @return bool
     */
    public function offsetExists($var)
    {
        return isset($this->$var) && is_callable([$this, $var]);
    }

    /**
     * @param mixed $var
     * @throws \Exception
     */
    public function offsetUnset($var)
    {
        throw new \Exception('Cannot delete '.$var);
    }
}
