<?php

namespace Hennig\Builder;

interface Jsonable
{
    /**
     * Must be called before json encode to prepare things
     */
    public function toJson();
}
