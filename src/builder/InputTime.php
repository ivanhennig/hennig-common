<?php

namespace Hennig\Builder;

/**
 * @inheritDoc
 */
class InputTime extends InputDate
{
    /** @var string */
    public string $subtype = self::ST_TIME;
}
