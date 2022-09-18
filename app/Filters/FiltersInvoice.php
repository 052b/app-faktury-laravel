<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersInvoice implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function($q) use ($value) {
            $q->where('invoice_number', $value)
                ->orWhere('site_contractor.name', 'LIKE', '%'.$value.'%');
        });
    }
}
