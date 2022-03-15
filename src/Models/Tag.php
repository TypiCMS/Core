<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\TagsModulePresenter;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\Modules\Projects\Models\Project;

class Tag extends Base
{
    use Historable;
    use PresentableTrait;

    protected $presenter = TagsModulePresenter::class;

    protected $guarded = [];

    public function scopePublished(Builder $query): Builder
    {
        return $query;
    }

    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    public function uri($locale = null): string
    {
        $locale = $locale ?: config('app.locale');
        $route = $locale.'::'.Str::singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->slug);
        }

        return '/';
    }
}
