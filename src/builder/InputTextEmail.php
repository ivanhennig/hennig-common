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
    public string $subtype = self::ST_EMAIL;
}
