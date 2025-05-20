<?php

namespace Hennig\Builder;

/**
 * Class InputHidden
 *
 * Render an input hidden
 *
 * @package Hennig\Builder
 */
class InputHidden extends InputCommon
{
    /** @var string */
    public string $type = self::TYP_TEXT;

    /** @var string */
    public string $subtype = self::ST_HIDDEN;

}
