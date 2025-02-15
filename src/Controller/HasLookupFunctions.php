<?php

namespace Hennig\Common\Controller;

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
    public function lookup($term, $filter = [])
    {
        if (is_array($term)) {
            return $this
                ->getModel()
                ->where($filter)
                ->orderBy('name')
                ->limit(100)
                ->get(['_id', 'name']);
        }

        $term = trim($term);
        return $this
            ->getModel()
            ->when($term, fn ($query) => $query
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('_id', $term)
            )
            )
            ->where($filter)
            ->orderBy('name')
            ->limit(100)
            ->get(['_id', 'name']);
    }
}
