<?php

namespace Hennig\Builder;


class InputBoolean extends InputSelect
{
    public $subtype = "boolean";

    public function init()
    {
        $this->items = [
            "" => __("-- Select one --"),
            "1 " => __("Yes"),
            "0 " => __("No")
        ];

        return parent::init();
    }
}
