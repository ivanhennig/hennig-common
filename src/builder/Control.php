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

    /**
     * @var string
     */
    public $grid_system = "col-md-6";

    /** @var bool */
    public $grid_break_after = false;

    /**
     * @var string
     */
    public $initialValue;

    /**
     * @var string[]
     */
    public $classes;

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

    /**
     * Bootstrap grid system
     *
     * Class prefix
     * .col-sm- .col-md- .col-lg- .col-xl-
     * >=540px  >=720px  >=960px  >=1140px
     *
     * @param string $grid_system
     * @return $this
     */
    public function setGridSystem($grid_system)
    {
        if ($grid_system === '100') {
            $grid_system = 'col-md-12';
        } elseif ($grid_system === '75') {
            $grid_system = 'col-md-8';
        } elseif ($grid_system === '50') {
            $grid_system = 'col-md-6';
        } elseif ($grid_system === '33') {
            $grid_system = 'col-md-4';
        } elseif ($grid_system === '25') {
            $grid_system = 'col-md-3';
        }

        $this->grid_system = $grid_system;
        return $this;
    }

    /**
     * @return $this
     */
    public function setGridBreakAfter()
    {
        $this->grid_break_after = true;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInitialValue($value)
    {
        $this->initialValue = $value;
        return $this;
    }

    /**
     * @param string[] $classes
     * @return $this
     */
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
        return $this;
    }
}
