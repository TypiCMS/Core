<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Tags\Models\Tag;

abstract class Base extends Model
{
    /**
     * Get preview uri.
     *
     * @return null|string string or null
     */
    public function previewUri()
    {
        if (!$this->id) {
            return '/';
        }

        return url($this->uri());
    }

    /**
     * Get public uri.
     *
     * @return string
     */
    public function uri($locale = null)
    {
        $locale = $locale ?: config('app.locale');
        $route = $locale.'::'.str_singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->slug);
        }

        return '/';
    }

    /**
     * Get published models.
     *
     * @param Builder $query
     *
     * @return Builder $query
     */
    public function scopePublished(Builder $query)
    {
        $field = 'status';
        if (in_array($field, $this->translatable)) {
            $field .= '->'.config('app.locale');
        }

        return $query->where($field, '1');
    }

    /**
     * Order items.
     *
     * @param Builder $query
     *
     * @return Builder $query
     */
    public function scopeOrder(Builder $query)
    {
        if ($order = config('typicms.'.$this->getTable().'.order')) {
            foreach ($order as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * A model has many tags.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->orderBy('tag')
            ->withTimestamps();
    }

    /**
     * Get back office’s edit url of model.
     *
     * @return string|void
     */
    public function editUrl()
    {
        $route = 'admin::edit-'.str_singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->id);
        }

        return route('dashboard');
    }

    /**
     * Get back office’s index of models url.
     *
     * @return string|void
     */
    public function indexUrl()
    {
        $route = 'admin::index-'.$this->getTable();
        if (Route::has($route)) {
            return route($route);
        }

        return route('dashboard');
    }

    public function scopeTranslated($query, $columns)
    {
        $translatableColumns = [];
        $locale = request('locale', config('app.locale'));
        if ($columns !== null) {
            $translatableColumns = explode(',', $columns);
        }
        foreach ($translatableColumns as $column) {
            $query
                ->selectRaw('
                    CASE WHEN
                    JSON_UNQUOTE(
                        JSON_EXTRACT(`'.$column.'`, \'$.'.$locale.'\')
                    ) = \'null\' THEN NULL
                    ELSE
                    JSON_UNQUOTE(
                        JSON_EXTRACT(`'.$column.'`, \'$.'.$locale.'\')
                    )
                    END
                    COLLATE utf8mb4_unicode_ci `'.$column.'_translated`
                ');
        }

        return $query;
    }
}
