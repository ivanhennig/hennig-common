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
    public $type = self::TYP_TEXT;

    /** @var string */
    public $subtype = self::ST_HIDDEN;

}
