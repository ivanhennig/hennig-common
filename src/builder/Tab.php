<?php

namespace Hennig\Builder;

class Tab extends Control
{

    public $title = "";

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}
