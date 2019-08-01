<?php

namespace Hennig\Common;

use Throwable;

class EFormatted extends \Exception
{
    public function __construct($message, $replacements)
    {
        parent::__construct(vsprintf($message, $replacements));
    }
}