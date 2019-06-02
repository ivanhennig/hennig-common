<?php

namespace Hennig\Common;

class EWrongSetting extends \Exception
{
    public function __construct($key)
    {
        parent::__construct("Please set $key setting.");
    }
}