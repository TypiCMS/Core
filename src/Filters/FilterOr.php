<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * @implements Filter<Model>
 */
class FilterOr implements Filter
{
    /** @return Builder<Model> */
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        $searchTerm = is_array($value) ? implode(',', $value) : $value;
        $columns = explode(',', $property);
        $locale = request()->string('locale')->value();

        return $query->where(function (Builder $query) use ($columns, $searchTerm, $locale): void {
            foreach ($columns as $column) {
                if (!$this->isTranslatable($query->getModel(), $column)) {
                    $query->orWhere($column, 'like', "%{$searchTerm}%");

                    continue;
                }

                $connection = $query->getConnection();

                match ($connection->getDriverName()) {
                    'pgsql' => $query->orWhereRaw(
                        "unaccent(\"{$column}\"::jsonb->>?) ILIKE unaccent(?)",
                        [$locale, "%{$searchTerm}%"]
                    ),
                    default => $query->orWhereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(`{$column}`, ?)) LIKE ? COLLATE " . ($connection->getConfig('collation') ?? 'utf8mb4_unicode_ci'),
                        ["$.{$locale}", "%{$searchTerm}%"]
                    ),
                };
            }
        });
    }

    private function isTranslatable(Model $model, string $column): bool
    {
        return property_exists($model, 'translatable') && in_array($column, (array) $model->translatable, true);
    }
}
