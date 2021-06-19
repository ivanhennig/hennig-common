<?php

namespace Hennig\Common\FTS;

class MongoFTSSearch
{
    /**
     * Scope a query that matches a full text search of term.
     *
     * @param Jenssegers\Mongodb\Eloquent\Model $query
     * @param string $term
     * @return Jenssegers\Mongodb\Eloquent\Model
     */
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->whereRaw(['$text' => ['$search' => $term]]);
    }
}
