<?php

namespace Hennig\Builder;

/**
 * Class InputSelect
 *
 * @package Hennig\Builder
 */
class InputSelect extends InputCommon
{
    /**
     * Component type, default text
     *
     * @var string
     */
    public $type = "select";
    public $subtype = "";
    public $multiselect = false;
    /**
     * Permite enviar itens na inicialização
     *
     * @var null
     */
    public $items = null;

    /**
     * @return InputCommon
     */
    public function init()
    {
        $this->placeholder = '--' . _('Select one') . '--';
        return parent::init();
    }

    /**
     * Whether accept multiple select
     *
     * @param bool $multiselect
     * @return InputSelect
     */
    public function setMultiselect($multiselect = true)
    {
        $this->multiselect = $multiselect;

        if ($this->placeholder === '--' . _('Select one') . '--') {
            $this->placeholder = '--' . _('Select one or many') . '--';
        }

        return $this;
    }

    /**
     * @param array|null $items
     * @return InputSelect
     */
    public function setItems($items)
    {
        if (is_array($items)) {
            $this->items = $items;
        }

        return $this;
    }
}
