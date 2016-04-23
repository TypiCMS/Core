<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use TypiCMS\Modules\Core\Facades\TypiCMS;
use TypiCMS\Modules\Core\Traits\HtmlCacheEvents;

abstract class Base extends Model
{
    use HtmlCacheEvents;

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
        $page = TypiCMS::getPageLinkedToModule($this->getTable());
        if ($page) {
            return $page->uri($locale).'/'.$this->translate($locale)->slug;
        }

        return '/';
    }

    /**
     * Attach files to model.
     *
     * @param Builder $query
     * @param bool    $all   : all models or online models
     *
     * @return Builder $query
     */
    public function scopeFiles(Builder $query, $all = false)
    {
        return $query->with(
            ['files' => function (Builder $query) use ($all) {
                $query->with(['translations' => function (Builder $query) use ($all) {
                    $query->where('locale', config('app.locale'));
                    !$all && $query->where('status', 1);
                }]);
                $query->whereHas('translations', function (Builder $query) use ($all) {
                    $query->where('locale', config('app.locale'));
                    !$all && $query->where('status', 1);
                });
                $query->orderBy('position', 'asc');
            }]
        );
    }

    /**
     * Get models that have online non empty translation.
     *
     * @param Builder $query
     *
     * @return Builder $query
     */
    public function scopeOnline(Builder $query)
    {
        $field = 'status';
        if (in_array('status', $this->translatable)) {
            $field .= '->'.config('typicms.content_locale');
        }

        return $query->where($field, 1);
    }

    /**
     * Get online galleries.
     *
     * @param Builder $query
     *
     * @return Builder $query
     */
    public function scopeWithOnlineGalleries(Builder $query)
    {
        if (!method_exists($this, 'galleries')) {
            return $query;
        }

        return $query->with(
            [
                'galleries.translations',
                'galleries.files.translations',
                'galleries' => function (MorphToMany $query) {
                    $query->whereHas(
                        'translations',
                        function (Builder $query) {
                            $query->where('status', 1);
                            $query->where('locale', config('app.locale'));
                        }
                    );
                },
            ]
        );
    }

    /**
     * Order items according to GET value or model value, default is id asc.
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
        return $this->morphToMany('TypiCMS\Modules\Tags\Models\Tag', 'taggable')
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
        try {
            return route('admin::edit-'.str_singular($this->getTable()), $this->id);
        } catch (InvalidArgumentException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Get back office’s index of models url.
     *
     * @return string|void
     */
    public function indexUrl()
    {
        try {
            return route('admin::index-'.$this->getTable());
        } catch (InvalidArgumentException $e) {
            Log::error($e->getMessage());
        }
    }
}
