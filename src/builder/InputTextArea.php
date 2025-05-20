<?php

namespace Hennig\Builder;

/**
 * Class InputTextArea
 *
 * @package Hennig\Builder
 */
class InputTextArea extends InputCommon
{
    /** @var string */
    public string $type = self::TYP_TEXTAREA;

    /** @var int */
    public $rows = 3;

    /**
     * @param int $rows
     * @return $this
     */
    public function setRows(int $rows)
    {
        $this->rows = $rows;
        return $this;
    }
}
