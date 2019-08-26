<?php

namespace Hennig\Builder;

/**
 * @inheritDoc
 */
class InputTime extends InputDate
{
    /** @var string */
    public $subtype = self::ST_TIME;
}
