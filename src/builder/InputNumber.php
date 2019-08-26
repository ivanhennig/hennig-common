<?php

namespace Hennig\Builder;

class InputNumber extends InputCommon
{
    /** @var string */
    public $type = self::TYP_NUMBER;

    /** @var string */
    public $subtype = self::ST_FLOAT;

    /** @var int */
    public $decimal = 2;
}
