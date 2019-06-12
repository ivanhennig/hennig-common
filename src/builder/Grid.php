<?php

namespace Hennig\Builder;

class Grid extends Card
{
    public $type = "grid";

    public $action = "index";
    /**
     * Nome único do grid. Obrigatório
     * @var string
     */
    public $name = "";
    public $limit = 100;
    /**
     * Default primary key field
     * @var string
     */
    public $recid = "_id";

    /**
     * Url to fetch records. Default: {model}/records
     * @var string
     */
    public $url = "";
    /**
     * Toolbar and footer configuration
     * @var array
     */
    public $show = [
        "toolbar" => true,
        "footer" => true,
        "toolbarAdd" => true,
        "toolbarEdit" => true,
        "toolbarSave" => true,
        "toolbarDelete" => true,
    ];
    /**
     * @var GridColumn[]
     */
    public $columns = [];

    public function __construct()
    {
        $this->name = uniqid();
    }

    public function offsetSet($offset, $value)
    {
        if ($value instanceof GridColumn) {
            $this->columns[] = $value;
        }
    }

    /**
     * Prepare the object to convert
     *
     * @return self
     */
    public function toJson()
    {
        if (!$this->url) {
            $this->url = $this->controller . "/records";
        }

        foreach ($this->columns as $column) {
            if (!isset($column->size)) {
                $column->size = "10%";
            }
        }
        return parent::toJson();
    }
}
