<?php

namespace Hennig\Builder;

class InputNumberCurrency extends InputNumber
{
    /** @var string */
    public string $subtype = self::ST_CURRENCY;
}
