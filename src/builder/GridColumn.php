<?php

namespace Hennig\Builder;

class GridColumn extends Control
{
    public $caption = ''; // column caption
    public $field = ''; // field name to map column to a record
    public $size = null; // size of column in px or %
    public $min = 15; // minimum width of column in px
    public $max = null; // maximum width of column in px
    public $gridMinWidth = null; // minimum width of the grid when column is visible
    public $sizeCorrected = null; // read only, corrected size (see explanation below)
    public $sizeCalculated = null; // read only, size in px (see explanation below)
    public $hidden = false; // indicates if column is hidden
    public $sortable = false; // indicates if column is sortable
    public $searchable = false; // indicates if column is searchable, bool/string: int,float,date,...
    public $resizable = true; // indicates if column is resizable
    public $hideable = true; // indicates if column can be hidden
    public $attr = ''; // string that will be inside the <td ... attr> tag
    public $style = ''; // additional style for the td tag
    public $render = null; // string or render function
    public $title = null; // string or function for the title property for the column cells
    public $editable = "{}"; // editable object if column fields are editable
    public $frozen = false; // indicates if the column is fixed to the left
    public $info = null;    // info bubble, can be bool/object

    /**
     * @param string $caption
     */
    public function setTitle($caption)
    {
        return $this->setCaption($caption);
    }

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @param string $field
     */
    public function setName($field)
    {
        return $this->setField($field);
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @param string[] $editable
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
        return $this;
    }

    public function setSortable($p = true)
    {
        $this->sortable = $p;
        return $this;
    }

}
