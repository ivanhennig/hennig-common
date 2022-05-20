<?php

namespace Hennig\Common\Controller;

use Hennig\Common\Exports\CollectionExport;
use Hennig\Common\FTS\MongoFTSSearch;
use Hennig\Common\FTS\MySQLFullTextSearch;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait HasGridFunctions
{
    use HasModelFunctions;

    protected $defaultSort = '_id';

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

        $page = $params['current'] ?? '' ?: 1;
        $limit = (int)($params['rowCount'] ?? '' ?: 25);
        $sort = $params['sort'] ?? [];

        $skip = ($page - 1) * $limit;
        /** @var Builder $builder */
        $builder = $this->getModel();

        $search = $params['search'] ?? [];
        $searchPhrase = $params['searchPhrase'] ?? '';

        $uses = class_uses($builder->getModel());
        if (array_intersect($uses, [MySQLFullTextSearch::class, MongoFTSSearch::class])) {
            $builder->search($searchPhrase);
        }

        $count = 0;
        if (empty($options['to_export'])) {
            $count = $builder->count();
        }

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
                } else if (in_array($column, array_keys($sortable))) {
                    if (!empty($sortable[$column]) && is_string($sortable[$column])) {
                        $builder->orderByRaw($sortable[$column]);
                    }  else if (array_key_exists('field', $sortable[$column])) {
                    $builder->orderBy($sortable[$column]['field'], $direction);
                }
            }
        }
        }

        if (empty($options['to_export'])) {
            $builder
                ->when($limit > 0, function ($builder) use ($limit, $skip) {
                    return $builder->skip($skip)->limit($limit);
                });
        }

        if (method_exists($this, 'beforeGetRows')) {
            $rows = $this->beforeGetRows($builder);
        } else {
            $rows = empty($options['to_export']) ? $builder->get() : $builder->cursor();
        }

        if (method_exists($this, 'getTransform')) {
            $rows = $rows->transform(function ($row) {
                return $this->getTransform($row);
            });
        }

        if (empty($options['to_export'])) {
            return [
                'rows' => $rows,
                'current' => (int)$page,
                'rowCount' => $limit,
                'total' => (int)$count
            ];
        }

        if (method_exists($this, 'getExportFields')) {
            $headers = $this->getExportFields();
            $rows = $rows->transform(function ($row) use ($headers) {
                $response = [];
                foreach ($headers as $k => $v) {
                    $response[$v] = Arr::get($row, $k , '');
                }
                return $response;
            });
        }

        return $rows;
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
