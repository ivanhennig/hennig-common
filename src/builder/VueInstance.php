<?php

namespace Hennig\Builder;

/**
 * Class VueInstance
 *
 * Render a special built Vue component
 *
 * @package Hennig\Builder
 */
class VueInstance extends InputCommon
{
    /** @var string */
    public string $type = 'vue';

    /** @var string Must have the name of globally registered component */
    public string $subtype = '';

    /** @var array */
    public $props = [];

    /**
     * @param string $name
     * @return $this
     */
    public function setComponentName(string $name)
    {
        $this->subtype = $name;
        return $this;
    }

    /**
     * @param array $props
     * @return $this
     */
    public function setComponentProps($props)
    {
        $this->props = $props;
        return $this;
    }
}
