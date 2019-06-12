<?php

namespace Hennig\Builder;


class InputSelect extends InputCommon
{
    /**
     * Component type, default text
     * @var string
     */
    public $type = "select";
    public $subtype = "";
    public $multiselect = false;
    /**
     * Permite enviar itens na inicialização
     * @var null
     */
    public $items = null;

    public function init()
    {
        $this->placeholder = __("-- Select one --");
        return parent::init();
    }

    /**
     * Configura o select para aceitar multiplos
     * @param bool $multiselect
     */
    public function setMultiselect($multiselect = true)
    {
        $this->multiselect = $multiselect;

        if ($this->placeholder === __("-- Select one --")) {
            $this->placeholder = __("-- Select one or many --");
        }


        return $this;
    }

    /**
     * @param mixex[]|null $items
     */
    public function setItems($items)
    {
        if (is_array($items)) {
            $this->items = $items;
        }
        return $this;
    }
}
