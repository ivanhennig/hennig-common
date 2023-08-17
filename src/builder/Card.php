<?php

namespace Hennig\Builder;

use ArrayAccess;

class Card implements ArrayAccess, Jsonable
{
    public $id;
    /**
     * Define o tipo de componente
     * @var string
     */
    public string $type = "";

    public function __construct()
    {
        $this->id = uniqid();
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function offsetExists($offset)
    {
    }

    public function offsetGet($offset)
    {
    }

    public function offsetUnset($offset)
    {
    }

    public function offsetSet($offset, $value)
    {
    }

    /**
     * Prepare the object to convert
     *
     * @return self
     */
    public function toJson()
    {
        return $this;
    }
}
