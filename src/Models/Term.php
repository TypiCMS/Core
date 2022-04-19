<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Route;
use Laracasts\Presenter\PresentableTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\TermPresenter;
use TypiCMS\Modules\Core\Traits\Historable;

class Term extends Base implements Sortable
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;
    use SortableTrait;

    protected $presenter = TermPresenter::class;

    protected $guarded = [];

    public $translatable = [
        'title',
        'slug',
    ];

    public $sortable = [
        'order_column_name' => 'position',
    ];

    public function buildSortQuery()
    {
        return static::query()->where('taxonomy_id', $this->taxonomy_id);
    }

    public function uri($locale = null): string
    {
        return url('/');
    }

    public function editUrl(): string
    {
        $route = 'admin::edit-term';
        if (Route::has($route)) {
            return route($route, [$this->taxonomy_id, $this->id]);
        }

        return route('admin::dashboard');
    }

    public function indexUrl(): string
    {
        $route = 'admin::index-terms';
        if (Route::has($route)) {
            return route($route, $this->taxonomy_id);
        }

        return route('admin::dashboard');
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    // public function publishedProjects(): MorphToMany
    // {
    //     return $this->morphedByMany(Project::class, 'model', 'model_has_terms')
    //         ->published()
    //         ->withTimestamps();
    // }
}
