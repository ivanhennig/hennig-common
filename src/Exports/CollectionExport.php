<?php

namespace Hennig\Common\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CollectionExport implements FromArray, WithHeadings
{
    private $headings;

    public function __construct($rows, $headings = [])
    {
        $this->rows = $rows;
        $this->headings = empty($headings) ? collect($rows->first())->keys()->toArray() : $headings;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
