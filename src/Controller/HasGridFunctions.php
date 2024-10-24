<?php

namespace Hennig\Common\Controller;

use Hennig\Common\Exports\CollectionExport;
use Hennig\Common\FTS\MongoFTSSearch;
use Hennig\Common\FTS\MySQLFullTextSearch;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;

trait HasGridFunctions
{
    use HasModelFunctions;

    protected $defaultSort = '_id';

    public function export($params)
    {
        return $this->records($params, ['to_export' => true]);
    }

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
        $skip_count = !empty($options['to_export']) || !empty($options['skip_count']);

        $skip = ($page - 1) * $limit;
        /** @var Builder $builder */
        $builder = $this->getModel();

        $search = $params['search'] ?? [];
        $searchPhrase = $params['searchPhrase'] ?? '';

        $uses = class_uses($builder->getModel());
        if (array_intersect($uses, [MySQLFullTextSearch::class])) {
            $builder->search($searchPhrase);
        } else if (array_intersect($uses, [MongoFTSSearch::class])) {
            $builder
                ->search($searchPhrase)
                ->project(['_score' => ['$meta' => 'textScore']])
                ->orderByRaw(['_score' => ['$meta' => 'textScore']]);
        }

        $this->getSearch($builder, $search, $searchPhrase);

        if ($skip_count) {
            $count = -1;
        } else {
            $count = $builder->count();
        }

        $sorted = [];
        if (empty($sort)) {
            $builder->orderByDesc($this->defaultSort);
            $sorted[$this->defaultSort] = 'desc';
        } else {
            $sortable = $builder->getModel()->sortable ?? [];
            foreach ($sort as $column => $direction) {
                if (in_array($column, $sortable)) {
                    if (is_string($direction)) {
                        $builder->orderBy($column, $direction);
                        $sorted[$column] = $direction;
                    }
                } else if (in_array($column, array_keys($sortable))) {
                    if (!empty($sortable[$column]) && is_string($sortable[$column])) {
                        $builder->orderByRaw($sortable[$column]);
                        $sorted[$column] = 'raw';
                    } else if (array_key_exists('field', $sortable[$column])) {
                        $builder->orderBy($sortable[$column]['field'], $direction);
                        $sorted[$column] = 'raw';
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
            $rows->map(function ($row) {
                return $this->getTransform($row);
            });
        }

        if (empty($options['to_export'])) {
            return [
                'rows' => $rows,
                'current' => (int)$page,
                'rowCount' => $limit,
                'total' => (int)$count,
                'sort' => $sorted,
            ];
        }

        if (empty($rows) || $rows->isEmpty()) {
            return [];
        }

        if (method_exists($this, 'getExportFields')) {
            $headers = array_keys($this->getExportFields());
            $headers_labels = array_values($this->getExportFields());
        } else {
            $headers = array_keys($rows->first()->toArray());
            $headers_labels = $headers;
        }

        $response = [];
        foreach ($rows as $key => $item) {
            $row = array_map(function ($v) {
                if (is_object($v) && enum_exists(get_class($v))) {
                    if (method_exists($v, 'toHuman')) {
                        return $v->toHuman();
                    }
                    return $v->value;
                }
                return $v;
            }, Arr::dot($item->toArray()));
            // Filter out unwanted fields
            $row = array_intersect_key($row, array_flip($headers));
            // Sort keys
            $row = array_replace(array_flip($headers), $row);
            $response[] = $row;
        }

        $export = new CollectionExport(
            $response,
            $headers_labels
        );
        $name = 'export' . uniqid() . '.xlsx';
        Excel::store($export, $name);
        return config('app.url') . "/download/$name";
    }

    /**
     * @param $model
     * @param array $search Additional query
     * @param string $searchPhrase Quick search input
     * @return mixed
     */
    abstract public function getSearch($model, $search, $searchPhrase);
}
