<?php

namespace Hennig\Builder;

class Button extends Control
{
    const BT_PRIMARY = "primary";
    const BT_SECONDARY = "secondary";
    const BT_SUCCESS = "success";
    const BT_INFO = "info";
    const BT_WARNING = "warning";
    const BT_DANGER = "danger";
    const BT_SAVE = "save";

    public $type = "button";
    public $subtype = Button::BT_PRIMARY;
    public $outline = false;

    public $default = false;
    public $on = [];

    /**
     * Whether the button must fire when enter key is pressed
     * @param bool $default
     * @return $this
     */
    public function setDefault($default = true)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Set alternative button style
     * @param boolean $outline
     * @return $this
     */
    public function setOutline($outline)
    {
        $this->outline = $outline;
        return $this;
    }
}
