<?php

namespace Hennig\Common\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasLookupFunctions
{
    /**
     * Method used by VueSelect
     *
     * @param string|array $term
     * @param array $filter
     * @return Collection
     */
    public function lookup($term, array $filter = [])
    {
        $builder = $this->getModel();
        $model = $builder->getModel();
        if (is_array($term)) {
            return $builder
                ->where($filter)
                ->orderBy('name')
                ->limit(100)
                ->get([$model->getKeyName(), 'name']);
        }

        $term = trim($term);
        return $builder
            ->when($term, fn ($query) => $query
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere($model->getKeyName(), $term)
            )
            )
            ->where($filter)
            ->orderBy('name')
            ->limit(100)
            ->get([$model->getKeyName(), 'name']);
    }
}
