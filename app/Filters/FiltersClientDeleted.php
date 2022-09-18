<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersClientDeleted implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function($q) use ($value) {
            $q->where('deleted', $value);
        });
    }
}
