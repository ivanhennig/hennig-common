<?php

namespace Hennig\Common;

use Throwable;

/**
 * Class ESimple
 * Simple error. Won't send notifications
 *
 * @package Hennig\Common
 */
class ESimple extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}