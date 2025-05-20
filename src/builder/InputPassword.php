<?php

namespace Hennig\Builder;

/**
 * Class InputPassword
 *
 * @package Hennig\Builder
 */
class InputPassword extends InputText
{
    /** @var string */
    public string $subtype = self::ST_PASSWORD;
}
