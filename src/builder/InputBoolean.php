<?php

namespace Hennig\Builder;

/**
 * Class InputBoolean
 * Render a button with Yes|No options
 *
 * @package Hennig\Builder
 */
class InputBoolean extends InputSelect
{
    public string $subtype = "boolean";

    /**
     * @return InputCommon
     */
    public function init()
    {
        $this->items = [
            '' => '--' . __('Select one') . '--',
            '1 ' => __('Yes'),
            '0 ' => __('No')
        ];

        return parent::init();
    }
}
