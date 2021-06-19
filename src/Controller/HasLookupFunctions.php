<?php

namespace Hennig\Common\Controller;

use Illuminate\Support\Collection;

trait HasLookupFunctions
{
    /**
     * Method used by VueSelect
     *
     * @param string $term
     * @param array $filter
     * @return Collection
     */
    public function lookup(string $term, $filter = [])
    {
        return $this
            ->getModel()
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('_id', $term)
            )
            ->where($filter)
            ->orderBy('name')
            ->limit(100)
            ->get(['_id', 'name']);
    }
}
