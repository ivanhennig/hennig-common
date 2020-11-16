<?php

namespace Hennig\Builder;

/**
 * Class InputHidden
 *
 * Render an Vue component
 *
 *
 * @package Hennig\Builder
 */
class VueInstance extends InputCommon
{
    /** @var string */
    public $type = 'vue';

    /** @var string Must have the name of globally registered component */
    public $subtype = '';

    /**
     * @param string $name
     */
    public function setComponentName(string $name): void
    {
        $this->subtype = $name;
    }
}
