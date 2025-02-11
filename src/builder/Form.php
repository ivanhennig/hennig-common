<?php

namespace Hennig\Builder;

class Form extends Card implements Jsonable, \JsonSerializable
{
    use HasEvents;

    public string $type = 'form';
    /** @var Control[] */
    public array $controls = [];
    /** @var Tab[] */
    public array $tabs = [];
    /** @var Button[] */
    public array $buttons = [];

    public array $data = [];

    public string $title = "";
    /** @var array Param for posting data via rpc */
    public $rpc = [];
    protected array $_controls = [];
    protected array $_tabs = [];
    protected $_last_tabref = "tab0";
    /**
     * Whether to include audit tab
     * @var bool
     */
    protected $_audit = true;

    public function __construct()
    {
        parent::__construct();

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

    public function jsonSerialize()
    {
        return $this->toJson();
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

            $this->controls[] = InputKey::Instance()
                ->setTabref('tabAudit')
                ->setName('created_by')
                ->setTitle(__('Created by'))
                ->setGridSystem('75')
                ->setGridBreakAfter()
                ->setReadonly(true);;

            $this->controls[] = InputText::Instance()
                ->setTabref('tabAudit')
                ->setName('created_by_display_text')
                ->setTitle('Nome')
                ->setGridSystem('75')
                ->setGridBreakAfter()
                ->setReadonly(true);

            $this->controls[] = InputDateTime::Instance()
                ->setTabref('tabAudit')
                ->setName('created_at')
                ->setTitle(__('Created at'))
                ->setGridSystem('75')
                ->setGridBreakAfter()
                ->setReadonly(true);

            $this->controls[] = InputKey::Instance()
                ->setTabref('tabAudit')
                ->setName('updated_by')
                ->setTitle(__('Updated by'))
                ->setGridSystem('75')
                ->setGridBreakAfter()
                ->setReadonly(true);;

            $this->controls[] = InputText::Instance()
                ->setTabref('tabAudit')
                ->setName('updated_by_display_text')
                ->setTitle('Nome')
                ->setGridSystem('75')
                ->setGridBreakAfter()
                ->setReadonly(true);

            $this->controls[] = InputDateTime::Instance()
                ->setTabref('tabAudit')
                ->setName('updated_at')
                ->setTitle(__('Updated at'))
                ->setGridSystem('75')
                ->setGridBreakAfter()
                ->setReadonly(true);
        }
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
}
