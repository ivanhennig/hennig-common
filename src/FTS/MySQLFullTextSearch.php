<?php

namespace Hennig\Common\FTS;

/**
 * Trait MySQLFullTextSearch
 * @method static |$this fullTextSearch(string $term)
 *
 * @package App\model
 */
trait MySQLFullTextSearch
{
    /**
     * Scope a query that matches a full text search of term.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFullTextSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        $query->where(function ($query) use ($term) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $columns = implode(',', $this->searchable);
            $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards($term));
            foreach ($this->searchable as $col) {
                $query->orWhere($col, 'like', '%' . $term . '%');
            }
        });
        return $query;
    }

    /**
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     * @return string
     */
    protected function fullTextWildcards($term)
    {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if (strlen($word) >= 3) {
                $words[$key] = '+' . $word . '*';
            }
        }

        $searchTerm = implode(' ', $words);

        return $searchTerm;
    }
}
