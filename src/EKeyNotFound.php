<?php

namespace Hennig\Common;

/**
 * Class EKeyNotFound
 *
 * @package Hennig\Common
 */
class EKeyNotFound extends \Exception
{
    public function __construct($key)
    {
        parent::__construct("Key $key was not found.");
    }
}
