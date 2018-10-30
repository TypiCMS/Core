<?php

namespace TypiCMS\Modules\Core\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterOr implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $columns = explode(',', $property);
        return $query->where(function (Builder $query) use ($columns, $value) {
            foreach ($columns as $column) {
                if (in_array($column, $query->getModel()->translatable)) {
                    $column = $column.'->'.request('locale');
                }
                $query->orWhere($column, 'like', '%'.$value.'%');
            }
        });
    }
}
