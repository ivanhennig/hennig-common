<?php

namespace Hennig\Builder;

class Form extends Card implements Jsonable
{
    use HasEvents;

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

    /** @var array Param for posting data via rpc */
    public $rpc = [];

    public function __construct()
    {
        $caller = last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2));
        $this->rpc = [
            class_basename($caller['class']),
            'save'
        ];
    }

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
                ->setId('tabAudit')
                ->setTitle(__('Audit'));

            $this->controls[] = InputText::Instance()
                ->setTabref('tabAudit')
                ->setName('created_by')
                ->setTitle(__('Created by'))
                ->setReadonly(true);

            $this->controls[] = InputDateTime::Instance()
                ->setTabref('tabAudit')
                ->setName('created_at')
                ->setTitle(__('Created at'))
                ->setReadonly(true);

            $this->controls[] = InputText::Instance()
                ->setTabref('tabAudit')
                ->setName('updated_by')
                ->setTitle(__('Updated by'))
                ->setReadonly(true);

            $this->controls[] = InputDateTime::Instance()
                ->setTabref('tabAudit')
                ->setName('updated_at')
                ->setTitle(__('Updated at'))
                ->setReadonly(true);
        }
        return $this;
    }
}
