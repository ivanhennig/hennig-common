<?php

namespace Hennig\Builder;

/**
 * Class InputTimeStamp
 * Render a instance of Tempus Dominus
 *
 * @package Hennig\Builder
 */
class InputDate extends InputCommon
{
    /** @var string */
    public $type = self::TYP_DATETIME;

    /** @var string */
    public $subtype = self::ST_DATE;
}
