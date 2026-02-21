<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasSelectableFields
{
    /** @param Builder<Model> $query */
    #[Scope]
    protected function selectFields(Builder $query): void
    {
        $locale = request('locale', app()->getLocale());
        $fields = explode(',', (string) request()->string('fields.' . $this->getTable()));

        foreach ($fields as $field) {
            if ($this->translatable === null || !$this->isTranslatableAttribute($field)) {
                $query->addSelect($field);

                continue;
            }

            $connection = $query->getConnection();
            $driver = $connection->getDriverName();

            if ($field === 'status') {
                match ($driver) {
                    'pgsql' => $query->selectRaw("({$field}::json->>?)::int AS {$field}_translated", [$locale]),
                    default => $query->selectRaw(
                        "CAST(JSON_UNQUOTE(JSON_EXTRACT(`{$field}`, ?)) AS UNSIGNED) AS `{$field}_translated`",
                        ["$.{$locale}"],
                    ),
                };

                continue;
            }

            match ($driver) {
                'pgsql' => $query->selectRaw("{$field}::json->>? AS {$field}_translated", [$locale]),
                default => $query->selectRaw(
                    "CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(`{$field}`, ?)) = 'null' THEN NULL ELSE JSON_UNQUOTE(JSON_EXTRACT(`{$field}`, ?)) END"
                    . (
                        $driver !== 'mariadb'
                            ? ' COLLATE ' . ($connection->getConfig('collation') ?? 'utf8mb4_unicode_ci')
                            : ''
                    )
                    . " AS `{$field}_translated`",
                    ["$.{$locale}", "$.{$locale}"],
                ),
            };
        }
    }
}
