<?php

namespace Hennig\Builder;

class InputNumberCurrency extends InputNumber
{
    /** @var string */
    public $subtype = self::ST_CURRENCY;
}
