<?php

namespace Hennig\Common\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CollectionExport implements FromCollection, WithHeadings
{
    private $collection;

    private $headings;

    /**
     * CollectionExport constructor.
     *
     * @param \Illuminate\Support\Collection $collection
     */
    public function __construct($collection, $headings = [])
    {
        $this->collection = $collection;
        $this->headings = empty($headings) ? collect($collection->first())->keys()->toArray() : $headings;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
