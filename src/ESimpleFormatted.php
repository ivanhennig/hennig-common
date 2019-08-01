<?php

namespace Hennig\Common;

use Throwable;

class ESimpleFormatted extends ESimple
{
    public function __construct($message, $replacements)
    {
        parent::__construct(vsprintf($message, $replacements));
    }
}