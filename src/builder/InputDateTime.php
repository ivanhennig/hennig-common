<?php

namespace Hennig\Builder;

/**
 * @inheritDoc
 */
class InputDateTime extends InputDate
{
    /** @var string */
    public string $subtype = self::ST_DATETIME;
}
