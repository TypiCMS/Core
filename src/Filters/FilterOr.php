<?php

namespace TypiCMS\Modules\Core\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class FilterOr implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $columns = explode(',', $property);

        return $query->where(function (Builder $query) use ($columns, $value) {
            foreach ($columns as $column) {
                if (in_array($column, (array) $query->getModel()->translatable)) {
                    $query->orWhereRaw(
                        'JSON_UNQUOTE(JSON_EXTRACT(`'.$column.'`, \'$.'.request('locale').'\')) LIKE \'%'.$value.'%\' COLLATE '.(DB::connection()->getConfig()['collation'] ?? 'utf8mb4_unicode_ci')
                    );
                } else {
                    $query->orWhere($column, 'like', '%'.$value.'%');
                }
            }
        });
    }
}
