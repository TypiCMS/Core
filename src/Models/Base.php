<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

/**
 * @property int $id
 * @property array<string> $translatable
 *
 * @method string url(string|null $locale = null)
 * @method mixed translate(string $field, string|null $locale = null)
 * @method bool isTranslatableAttribute(string $key)
 */
abstract class Base extends Model
{
    use Cachable;

    public function previewUrl(?string $locale = null): string
    {
        if (!$this->id) {
            return url('/');
        }

        $url = $this->url($locale);
        if (!is_string($url)) {
            return url('/');
        }

        return (string) Uri::of($url)->withQuery(['preview' => 'true']);
    }

    public function isPublished(?string $locale = null): bool
    {
        $locale ??= app()->getLocale();

        return (bool) $this->translate('status', $locale);
    }

    /** @param Builder<Model> $query */
    #[Scope]
    protected function published(Builder $query): void
    {
        if (
            !auth('web')->check()
            || auth('web')->user()->can('see unpublished items') && !request()->boolean('preview')
        ) {
            $field = 'status';
            if (in_array($field, $this->translatable, true)) {
                $field .= '->' . app()->getLocale();
            }

            $query->where($field, '1');
        }
    }

    /** @param Builder<Model> $query */
    #[Scope]
    protected function whereSlugIs(Builder $query, string $slug): void
    {
        $field = 'slug';
        if (in_array($field, $this->translatable, true)) {
            $field .= '->' . app()->getLocale();
        }

        $query->where($field, $slug);
    }

    /** @param Builder<Model> $query */
    #[Scope]
    protected function order(Builder $query): void
    {
        if ($order = config('typicms.modules.' . $this->getTable() . '.order')) {
            foreach ($order as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        }
    }

    /** @param Builder<Model> $query */
    #[Scope]
    protected function selectFields(Builder $query): void
    {
        $locale = request('locale', app()->getLocale());
        $fields = explode(',', (string) request()->string('fields.' . $this->getTable()));
        foreach ($fields as $field) {
            if (isset($this->translatable) && $this->isTranslatableAttribute($field)) {
                if ($field === 'status') {
                    if (config('typicms.postgresql') === true) {
                        $query->selectRaw('('
                        . $field
                        . "::json->>'"
                        . $locale
                        . "' )::int AS "
                        . $field
                        . '_translated');
                    } else {
                        $query->selectRaw(
                            '
                                CAST(JSON_UNQUOTE(
                                    JSON_EXTRACT(`'
                            . $field
                            . '`, \'$.'
                            . $locale
                            . '\')
                                ) AS UNSIGNED) AS `'
                            . $field
                            . '_translated`
                            ',
                        );
                    }
                } elseif (config('typicms.postgresql') === true) {
                    $query->selectRaw(
                        '
                                CASE WHEN
                                    '
                        . $field
                        . "::json->>'"
                        . $locale
                        . '\' = null
                                THEN
                                    NULL
                                ELSE
                                    '
                        . $field
                        . "::json->>'"
                        . $locale
                        . '\'
                                END
                                AS  '
                        . $field
                        . '_translated
                            ',
                    );
                } else {
                    $query->selectRaw(
                        '
                                CASE WHEN
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(`'
                        . $field
                        . '`, \'$.'
                        . $locale
                        . '\')
                                ) = \'null\' THEN NULL
                                ELSE
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(`'
                        . $field
                        . '`, \'$.'
                        . $locale
                        . '\')
                                )
                                END '
                        . (
                            config('typicms.mariadb') === false
                                ? 'COLLATE ' . (DB::connection()->getConfig()['collation'] ?? 'utf8mb4_unicode_ci')
                                : ''
                        )
                        . '
                                AS `'
                        . $field
                        . '_translated`
                            ',
                    );
                }
            } else {
                $query->addSelect($field);
            }
        }
    }

    /** @return Attribute<string, null> */
    protected function status(): Attribute
    {
        return Attribute::make(set: function ($status) {
            if (is_array($status)) {
                return json_encode(array_map(fn ($item): int => (int) $item, $status));
            }

            return $status;
        });
    }

    public function editUrl(): string
    {
        $route = 'admin::edit-' . Str::singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->id);
        }

        return route('admin::dashboard');
    }

    public function indexUrl(): string
    {
        $route = 'admin::index-' . $this->getTable();
        if (Route::has($route)) {
            return route($route);
        }

        return route('admin::dashboard');
    }

    public function next(mixed $model, ?int $category_id = null): ?Model
    {
        return $this->adjacent(1, $model, $category_id);
    }

    public function prev(mixed $model, ?int $category_id = null): ?Model
    {
        return $this->adjacent(-1, $model, $category_id);
    }

    public function adjacent(int $direction, mixed $model, ?int $category_id = null): ?Model
    {
        $currentModel = $model;
        if ($category_id !== null) {
            $models = self::query()
                ->published()
                ->with('category')
                ->order()
                ->where('category_id', $category_id)
                ->get(['id', 'category_id', 'slug']);
        } else {
            $models = self::query()
                ->published()
                ->order()
                ->get(['id', 'slug']);
        }

        foreach ($models as $key => $model) {
            if ($currentModel->id === $model->id) {
                $adjacentKey = $key + $direction;

                return $models[$adjacentKey] ?? null;
            }
        }

        return null;
    }
}
