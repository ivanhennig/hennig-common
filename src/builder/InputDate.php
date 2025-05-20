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
    public string $type = self::TYP_DATETIME;

    /** @var string */
    public string $subtype = self::ST_DATE;
}
