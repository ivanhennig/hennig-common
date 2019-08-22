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
    public $subtype = "boolean";

    /**
     * @return InputCommon
     */
    public function init()
    {
        $this->items = [
            '' => '--' . _('Select one') . '--',
            '1 ' => _('Yes'),
            '0 ' => _('No')
        ];

        return parent::init();
    }
}
