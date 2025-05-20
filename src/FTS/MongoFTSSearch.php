<?php

namespace Hennig\Common\FTS;

trait MongoFTSSearch
{
    /**
     * Scope a query that matches a full text search of term.
     */
    public function scopeFullTextSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->whereRaw(['$text' => ['$search' => $term]]);
    }
}
