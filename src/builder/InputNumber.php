<?php

namespace Hennig\Builder;

class InputNumber extends InputCommon
{
    /** @var string */
    public string $type = self::TYP_NUMBER;

    /** @var string */
    public string $subtype = self::ST_FLOAT;

    /** @var int */
    public $decimal = 2;
}
