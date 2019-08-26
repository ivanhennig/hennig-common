<?php

namespace Hennig\Builder;

class Control
{
    use HasEvents;

    public $id = "";
    /**
     * Name, match db field name
     *
     * @var string
     */
    public $name = "";
    /**
     * Label
     *
     * @var string
     */
    public $title = "";
    /**
     * Which tab must appear
     *
     * @var string
     */
    public $tabref = "";
    /**
     * Helper text when mouse is over
     *
     * @var string
     */
    public $placeholder = "";

    private function __construct()
    {
        $this->init();
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->id = uniqid();
        return $this;
    }

    /**
     * @return $this
     */
    static public function Instance()
    {
        $return = new static;
        return $return;
    }

    /**
     * Add the element to the form
     *
     * @param Form $a_form
     * @return $this
     */
    public function appendTo($a_form)
    {
        if (empty($this->name)) {
            $this->setName(get_class($this) . count($a_form->controls));
        }

        $a_form[] = $this;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $tabref
     * @return $this
     */
    public function setTabref($tabref)
    {
        $this->tabref = $tabref;
        return $this;
    }

    /**
     * Helper text when mouse is over
     *
     * @param string $value
     * @return $this
     */
    public function setPlaceholder($value)
    {
        $this->placeholder = $value;
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return $this
     */
    public function beforeJson()
    {
        return $this;
    }
}
