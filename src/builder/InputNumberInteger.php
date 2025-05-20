<?php

namespace Hennig\Builder;

/**
 * Class InputNumberInteger
 *
 * @package Hennig\Builder
 */
class InputNumberInteger extends InputNumber
{
    /** @var string */
    public string $subtype = self::ST_INTEGER;

    /** @var int */
    public $decimal = 0;
}
