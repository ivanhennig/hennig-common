<?php

namespace Hennig\Builder;

class ButtonSave extends Button
{
    /** @var string Use builtin function for postback data */
    public $action = 'save';

    public function init()
    {
        $this->title = __('Save');
        return parent::init();
    }
}
