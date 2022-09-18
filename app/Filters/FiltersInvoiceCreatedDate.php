<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersInvoiceCreatedDate implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function($q) use ($value) {
            $q->whereBetween('create_date', [$value[0], $value[1]]);
        });
    }
}
