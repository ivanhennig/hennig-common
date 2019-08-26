<?php

namespace Hennig\Builder;

/**
 * Class InputTextEmail
 *
 * @package Hennig\Builder
 */
class InputTextEmail extends InputText
{
    /** @var string */
    public $subtype = self::ST_EMAIL;
}
