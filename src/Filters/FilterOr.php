<?php

namespace TypiCMS\Modules\Core\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * @implements Filter<Model>
 */
class FilterOr implements Filter
{
    /** @return Builder<Model> */
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $columns = explode(',', $property);

        return $query->where(function (Builder $query) use ($columns, $value) {
            foreach ($columns as $column) {
                if (in_array($column, (array) $query->getModel()->translatable)) {
                    $query->orWhereRaw(
                        'JSON_UNQUOTE(JSON_EXTRACT(`' . $column . '`, \'$.' . request()->string('locale') . '\')) LIKE \'%' . $value . '%\' COLLATE ' . (DB::connection()->getConfig()['collation'] ?? 'utf8mb4_unicode_ci')
                    );
                } else {
                    $query->orWhere($column, 'like', '%' . $value . '%');
                }
            }
        });
    }
}
