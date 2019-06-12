<?php

namespace Hennig\Builder;

class Form extends Card implements Jsonable
{
    public $type = "form";
    /**
     *
     * @var Control[]
     */
    public $controls = [];
    /**
     *
     * @var Tab[]
     */
    public $tabs = [];
    /**
     *
     * @var Button[]
     */
    public $buttons = [];
    public $title = "";
    protected $_controls = [];
    protected $_tabs = [];
    protected $_last_tabref = "tab0";
    /**
     * Whether to include audit tab
     * @var bool
     */
    protected $_audit = true;


    /**
     * @param bool $audit
     * @return $this
     */
    public function setAudit($audit)
    {
        $this->_audit = $audit;
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
     * @return Control[]
     */
    public function getControls()
    {
        return $this->_controls;
    }

    /**
     * AppendTo
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof Tab) {
            $this->_last_tabref = $value->id;
            $this->_tabs[] = $value;
        } else if ($value instanceof Button) {
            $this->buttons[] = $value;
        } else if ($value instanceof Control) {
            if (!$value->tabref) {
                $value->tabref = $this->_last_tabref;
            }
            $this->_controls[] = $value;
        }
    }

    /**
     * Prepare the object to convert
     *
     * @return $this
     */
    public function toJson()
    {
        foreach ($this->_controls as $_control) {
            $_control->beforeJson();
        }
        $this->controls = $this->_controls;
        $this->tabs = $this->_tabs;

        if ($this->_audit) {


            $this->tabs[] = Tab::Instance()
                ->setId("tabAudit")
                ->setTitle("Audit");

            $this->controls[] = InputTimeStamp::Instance()
                ->setTabref("tabAudit")
                ->setName("created_at")
                ->setTitle("Created at")
                ->setReadonly(true);

            $this->controls[] = InputTimeStamp::Instance()
                ->setTabref("tabAudit")
                ->setName("updated_at")
                ->setTitle("Updated at")
                ->setReadonly(true);
        }
        return $this;
    }
}
