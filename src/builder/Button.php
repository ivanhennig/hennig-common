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

    /**
     * Set event handlers
     * Ex.:
     *  ->on('click','js_function_name');
     *
     * @param $method
     * @param $value
     * @return $this
     */
    public function on($method, $value)
    {
        $this->on[$method] = $value;
        return $this;
    }
}
