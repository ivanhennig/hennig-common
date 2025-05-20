<?php

namespace Hennig\Builder;

class InputCommon extends Control
{
    const ALIGN_LEFT = "left";
    const ALIGN_RIGHT = "right";
    const ALIGN_CENTER = "center";

    const TYP_TEXT = 'text';
    const TYP_NUMBER = 'number';
    const TYP_SELECT = 'select';
    const TYP_TEXTAREA = 'textarea';
    const TYP_DATETIME = 'datetime';

    const ST_HIDDEN = 'hidden';
    const ST_FLOAT = 'float';
    const ST_CURRENCY = 'currency';
    const ST_INTEGER = 'integer';
    const ST_PASSWORD = 'password';
    const ST_EMAIL = 'email';
    const ST_DATE = 'date';
    const ST_TIME = 'time';
    const ST_DATETIME = 'datetime';
    /**
     * Component type, default text
     * @var string
     */
    public string $type = self::TYP_TEXT;
    public string $subtype = "";
    /**
     * Uniq id, randomly created
     * @var string
     */
    public $id = "";
    /**
     * Texto, se informado, será posicionado abaixo do input
     * @var string
     */
    public string $help = "";

    /**
     * Texto, se informado, será posicionado dentro do input
     */
    public $placeholder = "";
    public string $align = InputCommon::ALIGN_LEFT;
    public bool $required = false;
    public bool $readonly = false;

    /**
     * Texto, se informado, será posicionado abaixo do input
     */
    public function setHelp(string $help): InputCommon {
        $this->help = $help;
        return $this;
    }

    public function setRequired(bool $required): InputCommon {
        $this->required = $required;
        return $this;
    }

    public function setReadonly(bool $readonly): InputCommon {
        $this->readonly = $readonly;
        return $this;
    }

    public function setAlign(string $align): InputCommon {
        $this->align = $align;
        return $this;
    }

    public function beforeJson(): Control {
        if ($this->name === '_id' || $this->name === 'id') {//Forçar readonly
            $this->readonly = true;
        }
        return parent::beforeJson();
    }

}
