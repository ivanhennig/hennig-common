<?php

namespace Hennig\Common\Controller;

use Hennig\Common\Exports\CollectionExport;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait HasGridFunctions
{
    use HasModelFunctions;

    /**
     * @param $model
     * @param array $search Additional query
     * @param string $searchPhrase Quick search input
     * @return mixed
     */
    abstract public function getSearch($model, $search, $searchPhrase);

    /**
     * Common method use by bootgrid
     * current=1&rowCount=10&sort[sender]=asc&searchPhrase=
     *
     * @return array
     */
    public function records($params = null, $options = [])
    {
        $params = $params ?? Request::all();
        $page = $_POST['current'] ?? '' ?: 1;
        $limit = (int)($_POST['rowCount'] ?? '' ?: 25);
        $sort = $params['sort'] ?? [];

        $skip = ($page - 1) * $limit;
        /** @var Builder $builder */
        $builder = $this->getModel();

        $uses = implode(',', class_uses($builder));
        if (Str::match('MySQLFullTextSearch', $uses)) {
            $builder->search($params['searchPhrase'] ?? []);
        }

        $count = 0;
        if (!empty($options['to_export'])) {
            $count = $builder->count();
        }

        $search = $params['search'] ?? [];
        $searchPhrase = $params['searchPhrase'] ?? '';
        $this->getSearch($builder, $search, $searchPhrase);

        if (empty($sort)) {
            $builder->orderByDesc($this->defaultSort);
        } else {
            $sortable = $builder->getModel()->sortable ?? [];
            foreach ($sort as $column => $direction) {
                if (in_array($column, $sortable)) {
                    if (is_string($direction)) {
                        $builder->orderBy($column, $direction);
                    }
                } else if (array_key_exists($column, $sortable)) {
                    $builder->orderBy($sortable[$column]['field'], $direction);
                }
            }
        }

        if (!empty($options['to_export'])) {
            $rows = $builder->cursor();
        } else {
            $rows = $builder
                ->when($limit > 0, function ($builder) use ($limit, $skip) {
                    return $builder->skip($skip)->limit($limit);
                })
                ->get();
        }

        if (method_exists($this, 'getTransform')) {
            $rows = $rows->transform(function ($row) {
                return $this->getTransform($row);
            });
        }

        if (!empty($options['to_export'])) {
            return $rows;
        }

        return [
            'rows' => $rows,
            'current' => (int)$page,
            'rowCount' => $limit,
            'total' => (int)$count
        ];
    }

    public function export()
    {
        $rows = $this->records(Request::all(), ['to_export' => true]);

        $export = new CollectionExport($rows);
        $name = 'export' . uniqid() . '.xlsx';
        Excel::store($export, $name);
        return "/download/$name";
    }
}
