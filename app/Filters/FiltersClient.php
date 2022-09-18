<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersClient implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function($q) use ($value) {
            $q->where('name', 'LIKE', '%'.$value.'%')
                ->orWhere('address','LIKE', '%'.$value.'%')
                ->orWhere('address2','LIKE', '%'.$value.'%')
                ->orWhere('zip','LIKE', '%'.$value.'%')
                ->orWhere('city','LIKE', '%'.$value.'%')
                ->orWhere('nip','LIKE', '%'.$value.'%')
                ->orWhere('phone','LIKE', '%'.$value.'%')
                ->orWhere('mail','LIKE', '%'.$value.'%');

        });
    }
}
