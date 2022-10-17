<?php

namespace TypiCMS\Modules\Core\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class Base extends Model
{
    use Cachable;

    public function previewUri(): string
    {
        $uri = '/';
        if ($this->id) {
            $uri = $this->uri();
        }

        return url($uri);
    }

    public function uri($locale = null): string
    {
        $locale = $locale ?: config('app.locale');
        $route = $locale.'::'.Str::singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->translate('slug', $locale));
        }

        return url('/');
    }

    public function isPublished($locale = null): bool
    {
        $locale = $locale ?: app()->getLocale();

        return (bool) $this->translate('status', $locale);
    }

    public function scopePublished(Builder $query): Builder
    {
        if (auth('web')->check() && auth('web')->user()->can('see unpublished items') && request('preview')) {
            return $query;
        }
        $field = 'status';
        if (in_array($field, (array) $this->translatable)) {
            $field .= '->'.config('app.locale');
        }

        return $query->where($field, '1');
    }

    public function scopeWhereSlugIs($query, $slug): Builder
    {
        $field = 'slug';
        if (in_array($field, (array) $this->translatable)) {
            $field .= '->'.config('app.locale');
        }

        return $query->where($field, $slug);
    }

    public function scopeOrder(Builder $query): Builder
    {
        if ($order = config('typicms.modules.'.$this->getTable().'.order')) {
            foreach ($order as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        }

        return $query;
    }

    public function scopeSelectFields($query, string $fields): Builder
    {
        $locale = request('locale', config('app.locale'));
        $fields = explode(',', $fields);
        foreach ($fields as $field) {
            if (isset($this->translatable) && $this->isTranslatableAttribute($field)) {
                if ($field === 'status') {
                    if (config('typicms.postgresql') === true) {
                        $query->selectRaw('('.$field.'::json->>\''.$locale.'\' )::int AS '.$field.'_translated');
                    } else {
                        $query
                            ->selectRaw('
                                CAST(JSON_UNQUOTE(
                                    JSON_EXTRACT(`'.$field.'`, \'$.'.$locale.'\')
                                ) AS UNSIGNED) AS `'.$field.'_translated`
                            ');
                    }
                } else {
                    if (config('typicms.postgresql') === true) {
                        $query
                            ->selectRaw('
                                CASE WHEN
                                    '.$field.'::json->>\''.$locale.'\' = null
                                THEN
                                    NULL
                                ELSE
                                    '.$field.'::json->>\''.$locale.'\'
                                END
                                AS  '.$field.'_translated
                            ');
                    } else {
                        $query
                            ->selectRaw('
                                CASE WHEN
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(`'.$field.'`, \'$.'.$locale.'\')
                                ) = \'null\' THEN NULL
                                ELSE
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(`'.$field.'`, \'$.'.$locale.'\')
                                )
                                END '.
                                (config('typicms.mariadb') === false ? 'COLLATE '.(DB::connection()->getConfig()['collation'] ?? 'utf8mb4_unicode_ci') : '').'
                                AS `'.$field.'_translated`
                            ');
                    }
                }
            } else {
                $query->addSelect($field);
            }
        }

        return $query;
    }

    public function setStatusAttribute($status)
    {
        if (is_array($status)) {
            $this->attributes['status'] = json_encode(array_map(function ($item) {
                return (int) $item;
            }, $status));
        } else {
            $this->attributes['status'] = $status;
        }
    }

    public function editUrl(): string
    {
        $route = 'admin::edit-'.Str::singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->id);
        }

        return route('admin::dashboard');
    }

    public function indexUrl(): string
    {
        $route = 'admin::index-'.$this->getTable();
        if (Route::has($route)) {
            return route($route);
        }

        return route('admin::dashboard');
    }

    public function next(Model $model, int $category_id = null): ?Model
    {
        return $this->adjacent(1, $model, $category_id);
    }

    public function prev(Model $model, int $category_id = null): ?Model
    {
        return $this->adjacent(-1, $model, $category_id);
    }

    public function adjacent(int $direction, Model $model, int $category_id = null): ?Model
    {
        $currentModel = $model;
        if ($category_id !== null) {
            $models = $this->published()
                ->with('category')
                ->order()
                ->where('category_id', $category_id)
                ->get(['id', 'category_id', 'slug']);
        } else {
            $models = $this->published()->order()->get(['id', 'slug']);
        }
        foreach ($models as $key => $model) {
            if ($currentModel->id === $model->id) {
                $adjacentKey = $key + $direction;

                return isset($models[$adjacentKey]) ? $models[$adjacentKey] : null;
            }
        }

        return null;
    }
}
