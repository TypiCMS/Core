<?php

namespace TypiCMS\Modules\Core\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use TypiCMS\Modules\Core\Facades\TypiCMS;
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
        $page = TypiCMS::getPageLinkedToModule($this->getTable());
        if ($page) {
            return $page->uri($locale).'/'.$this->translate('slug', $locale);
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
        try {
            return route('admin::edit-'.str_singular($this->getTable()), $this->id);
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function scopeTranslated($query, array $columns)
    {
        foreach ($columns as $column) {
            $query->selectRaw('`'.$column.'`->>"$.'.request('locale', config('app.locale')).'" COLLATE utf8mb4_unicode_ci `'.$column.'_translated`');
        }

        return $query;
    }
}
