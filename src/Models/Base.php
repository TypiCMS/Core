<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use TypiCMS\Modules\Core\Facades\TypiCMS;

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
        $page = TypiCMS::getPageLinkedToModule($this->getTable());
        if ($page) {
            return $page->uri($locale).'/'.$this->translate('slug', $locale);
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
                !$all && $query->where('status->'.config('app.locale'), '1');
                $query->orderBy('position', 'asc');
            }]
        );
    }

    /**
     * Get online models.
     *
     * @param Builder $query
     *
     * @return Builder $query
     */
    public function scopeOnline(Builder $query)
    {
        $field = 'status';
        if (in_array($field, $this->translatable)) {
            $field .= '->'.config('app.locale');
        }

        return $query->where($field, '1');
    }

    /**
     * Get online galleries.
     *
     * @param Builder $query
     *
     * @return Builder $query
     */
    public function scopeWithOnlineGalleries(Builder $query, $all = false)
    {
        if (!method_exists($this, 'galleries')) {
            return $query;
        }

        return $query->with(
            [
                'galleries.files',
                'galleries' => function (MorphToMany $query) {
                    !$all && $query->where('status->'.config('app.locale'), '1');
                },
            ]
        );
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
