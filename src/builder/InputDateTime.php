<?php

namespace Hennig\Builder;

/**
 * @inheritDoc
 */
class InputDateTime extends InputDate
{
    /** @var string */
    public $subtype = self::ST_DATETIME;
}
