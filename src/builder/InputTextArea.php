<?php

namespace Hennig\Builder;

class InputTextArea extends InputCommon
{
    public $type = "textarea";
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
